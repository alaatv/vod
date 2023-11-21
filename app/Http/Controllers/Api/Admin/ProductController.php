<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\ProductAdminSearch;
use App\Collection\ProductCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditProductRequest;
use App\Http\Requests\InsertProductRequest;
use App\Http\Requests\ProductBulkUpdateStatusesRequest;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductSelfRelationRequest;
use App\Http\Requests\SetProductDiscountRequest;
use App\Http\Requests\UpdateProductAttributeValueRequest;
use App\Http\Resources\Admin\AttributeValueProductResource;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\Price as PriceResource;
use App\Http\Resources\ProductSetLiteResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Product;
use App\Traits\CharacterCommon;
use App\Traits\ProductCommon;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

/**
 * Class ProductController.
 * For Api Version 2.
 * For Admin side.
 *
 * @package App\Http\Controllers\Api\Admin
 */
class ProductController extends Controller
{
    use CharacterCommon;
    use ProductCommon;

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_PRODUCT_ACCESS'), ['only' => 'index']);
        $this->middleware('permission:'.config('constants.INSERT_PRODUCT_ACCESS'), ['only' => 'store']);
        $this->middleware('permission:'.config('constants.SHOW_PRODUCT_ACCESS'), ['only' => 'show']);
        $this->middleware('permission:'.config('constants.EDIT_PRODUCT_ACCESS'),
            ['only' => ['update', 'bulkUpdateStatuses']]);
        $this->middleware('permission:'.config('constants.REMOVE_PRODUCT_ACCESS'), ['only' => 'destroy']);
        $this->middleware('permission:'.config('constants.SET_DISCOUNT_FOR_PRODUCT'), ['only' => 'setDiscount']);
    }

    /**
     * Return a listing of the resource.
     *
     * @param  ProductAdminSearch  $productSearch
     * @return ResourceCollection
     */
    public function index(ProductIndexRequest $request, ProductAdminSearch $productSearch)
    {
        $filters = $request->all();
        $filters['doesntHaveGrand'] = 1;
        $pageName = Product::INDEX_PAGE_NAME;
        $productSearch->setPageName($pageName);
        // Set the number of items on each page.
        if ($request->has('length') && $request->length > 0) {
            $productSearch->setNumberOfItemInEachPage($request->length);
        }

        // Filter resources based on received parameters.
        $productResult = $productSearch->get($filters);
        return ProductResource::collection($productResult);
    }

    /**
     * Return the specified resource.
     *
     * @param  Product  $product
     * @return JsonResponse|ProductResource|RedirectResponse|Redirector
     */
    public function show(Product $product)
    {
        if (isset($product->redirectUrl)) {
            $redirectUrl = $product->redirectUrl;
            return redirect(convertRedirectUrlToApiVersion($redirectUrl['url'], '2'),
                $redirectUrl['code'], request()->headers->all());
        }

        if (!is_null($product->grandParent)) {
            return redirect($product->grandParent->apiUrl['v1'], Response::HTTP_MOVED_PERMANENTLY,
                request()->headers->all());
        }


        return response()->json(new ProductResource($product), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertProductRequest|Product  $request
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function store(InsertProductRequest $request)
    {
        $product = new Product();
        $bonPlus = $request->bonPlus;
        $bonDiscount = $request->bonDiscount;
        $bonId = $request->bon_id;

        $this->fillProductFromRequest($request->all(), $product);

        try {
            $product->save();
            if ($bonPlus || $bonDiscount) {
                $this->attachBonToProduct($product, $bonId, $bonDiscount, $bonPlus);
            }
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(new ProductResource($product->refresh()), Response::HTTP_CREATED);
    }

    /**
     * Update attribute values.
     *
     * @param  UpdateProductAttributeValueRequest  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function updateAttributeValues(UpdateProductAttributeValueRequest $request, Product $product)
    {
        /*
        Notice! The input parameter should be an array as follows.
        Notice! In the following examples, keys 49 and 69 are the attribute table id.

        'attribute_values' => [
            0 => [
                'id' => 49,
                'order' => 20,
                'extraCost' => 120000,
                'description' => 'Long description for test',
            ],
            1 => [
                'id' => 69,
                'order' => 25,
                'extraCost' => 1950000,
                'description' => 'Long description for test',
            ],
            ...
        ]

        attribute_values[0][id]:49
        attribute_values[0][order]:20
        attribute_values[0][extraCost]:120000
        attribute_values[0][description]:Long description for test
        attribute_values[1][id]:69
        attribute_values[1][order]:20
        attribute_values[1][extraCost]:120000
        attribute_values[1][description]:Long description for test
        ...

        */

        $validatedParams = $request->validated();

        // TODO: If we can apply validation rules to the keys of input array, we no longer need to use the following code.
        // Re-format the input parameters for use within the sync method.
        $params = reformatToUseInSync($validatedParams['attribute_values']);

        try {
            $product->attributevalues()->sync($params);
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }
        return (new ProductResource($product->refresh()))->response();
    }


    public function indexAttributeValues(Product $product)
    {
        return AttributeValueProductResource::collection($product->attributevalues);
    }

    /**
     * Return a listing of the product's attribute values.
     *
     * @param  Product  $product
     * @return ResourceCollection
     */
    // TODO: The following method neither returns the pagination output nor searches the final list.
    /**
     * Remove the specified resource from storage.
     *
     * @param  Product  $product
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  Request  $request
     * @param  Product  $grandProduct
     * @return Response
     */
    public function refreshPrice(Request $request, Product $grandProduct)
    {
        $mainAttributeValues = $request->mainAttributeValues;
        $selectedSubProductIds = $request->products;
        $extraAttributeValues = $request->extraAttributeValues;

        $user = $request->user();

        $key =
            'product:refreshPrice::'.$grandProduct->cacheKey()."-user\\".(isset($user) && !is_null($user) ? $user->cacheKey() : '')."-mainAttributeValues\\".(isset($mainAttributeValues) ? implode('',
                $mainAttributeValues) : '-')."-subProducts\\".(isset($selectedSubProductIds) ? implode('',
                $selectedSubProductIds) : '-')."-extraAttributeValues\\".(isset($extraAttributeValues) ? implode('',
                $extraAttributeValues) : '-');

        return Cache::tags('bon')
            ->remember($key, config('constants.CACHE_60'), function () use (
                $grandProduct,
                $user,
                $mainAttributeValues,
                $selectedSubProductIds,
                $extraAttributeValues
            ) {
                $grandProductType = optional($grandProduct->producttype)->id;
                $intendedProducts = collect();
                switch ($grandProductType) {
                    case config('constants.PRODUCT_TYPE_SIMPLE'):
                        $intendedProducts->push($grandProduct);
                        break;
                    case config('constants.PRODUCT_TYPE_CONFIGURABLE'):
                        $simpleProduct = $this->findProductChildViaAttributes($grandProduct, $mainAttributeValues);
                        if (isset($simpleProduct)) {
                            $intendedProducts->push($simpleProduct);
                        }

                        break;
                    case config('constants.PRODUCT_TYPE_SELECTABLE'):
                        if (isset($selectedSubProductIds)) {
                            /** @var ProductCollection $selectedSubProducts */
                            $selectedSubProducts = Product::query()->whereIn('id', $selectedSubProductIds)
                                ->get();
                            $selectedSubProducts->load('parents');
                            $selectedSubProducts->keepOnlyParents();

                            $intendedProducts = $selectedSubProducts;
                        }
                        break;
                    default :
                        break;
                }

                $cost = 0;
                $costForCustomer = 0;
                $outOfStocks = collect();
                $error = false;
                if ($intendedProducts->isNotEmpty()) {
                    foreach ($intendedProducts as $product) {
                        if ($product->isInStock()) {
                            if (isset($user)) {
                                $costArray = $product->calculatePayablePrice($user);
                            } else {
                                $costArray = $product->calculatePayablePrice();
                            }

                            $cost += $costArray['cost'];
                            $costForCustomer += $costArray['customerPrice'];
                        } else {
                            $outOfStocks->push([
                                'id' => $product->id,
                                'name' => $product->name,
                            ]);
                        }
                    }
                } else {
                    $error = true;
                    $errorCode = Response::HTTP_NOT_FOUND;
                    $errorText = 'No products found';
                }

                $totalExtraCost = 0;
                if (is_array($extraAttributeValues)) {
                    $totalExtraCost = $this->productExtraCostFromAttributes($grandProduct, $extraAttributeValues);
                }

                if ($error) {
                    $result = [
                        'error' => [
                            'code' => $errorCode ?? $errorCode,
                            'message' => $errorText ?? $errorText,
                        ],
                    ];
                } else {
                    $result = [
                        'outOfStock' => $outOfStocks->isEmpty() ? null : $outOfStocks,
                        'cost' => [
                            'base' => $cost,
                            'discount' => $cost - $costForCustomer,
                            'final' => $costForCustomer + $totalExtraCost,
                        ],
                    ];
                }

                return json_encode($result, JSON_UNESCAPED_UNICODE);
            });
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     * @param  Product  $grandProduct
     * @return mixed
     */
    public function refreshPriceV2(Request $request, Product $grandProduct)
    {
        $mainAttributeValues = $request->mainAttributeValues;
        $selectedSubProductIds = $request->products;
        $extraAttributeValues = $request->extraAttributeValues;

        $user = $request->user();

        $key =
            'product:refreshPricev2:'.$grandProduct->cacheKey()."-user\\".(isset($user) && !is_null($user) ? $user->cacheKey() : '')."-mainAttributeValues\\".(isset($mainAttributeValues) ? implode('',
                $mainAttributeValues) : '-')."-subProducts\\".(isset($selectedSubProductIds) ? implode('',
                $selectedSubProductIds) : '-')."-extraAttributeValues\\".(isset($extraAttributeValues) ? implode('',
                $extraAttributeValues) : '-');

        return Cache::tags('bon')
            ->remember($key, config('constants.CACHE_60'), function () use (
                $grandProduct,
                $user,
                $mainAttributeValues,
                $selectedSubProductIds,
                $extraAttributeValues
            ) {
                $grandProductType = optional($grandProduct->producttype)->id;
                $intendedProducts = collect();
                switch ($grandProductType) {
                    case config('constants.PRODUCT_TYPE_SIMPLE'):
                        $intendedProducts->push($grandProduct);
                        break;
                    case config('constants.PRODUCT_TYPE_CONFIGURABLE'):
                        $simpleProduct = $this->findProductChildViaAttributes($grandProduct, $mainAttributeValues);
                        if (isset($simpleProduct)) {
                            $intendedProducts->push($simpleProduct);
                        }

                        break;
                    case config('constants.PRODUCT_TYPE_SELECTABLE'):
                        if (isset($selectedSubProductIds)) {
                            /** @var ProductCollection $selectedSubProducts */
                            $selectedSubProducts = Product::query()->whereIn('id', $selectedSubProductIds)
                                ->get();
                            $selectedSubProducts->load('parents');
                            $selectedSubProducts->keepOnlyParents();

                            $intendedProducts = $selectedSubProducts;
                        }
                        break;
                    default :
                        break;
                }

                $cost = 0;
                $costForCustomer = 0;
                $outOfStocks = collect();
                $error = false;
                if ($intendedProducts->isNotEmpty()) {
                    foreach ($intendedProducts as $product) {
                        if ($product->isInStock()) {
                            if (isset($user)) {
                                $costArray = $product->calculatePayablePrice($user);
                            } else {
                                $costArray = $product->calculatePayablePrice();
                            }

                            $cost += $costArray['cost'];
                            $costForCustomer += $costArray['customerPrice'];
                        } else {
                            $outOfStocks->push([
                                'id' => $product->id,
                                'name' => $product->name,
                            ]);
                        }
                    }
                } else {
                    $error = true;
                    $errorCode = Response::HTTP_NOT_FOUND;
                    $errorText = 'No products found';
                }

                $totalExtraCost = 0;
                if (is_array($extraAttributeValues)) {
                    $totalExtraCost = $this->productExtraCostFromAttributes($grandProduct, $extraAttributeValues);
                }

                if ($error) {
                    return json_encode([
                        'error' => [
                            'code' => $errorCode ?? $errorCode,
                            'message' => $errorText ?? $errorText,
                        ],
                    ], JSON_UNESCAPED_UNICODE);
                }
                $costInfo = [
                    'base' => $cost,
                    'discount' => $cost - $costForCustomer,
                    'final' => $costForCustomer + $totalExtraCost,
                ];

                return new PriceResource($costInfo);
            });
    }

    public function fetchProducts(Request $request)
    {
        $since = $request->timestamp;

        $products = Product::active()->whereNull('grand_id');
        if (!is_null($since)) {
            $products->where(function ($q) use ($since) {
                $q->where('created_at', '>=', Carbon::createFromTimestamp($since))
                    ->orWhere('updated_at', '>=', Carbon::createFromTimestamp($since));
            });
        }
        $products = $products->paginate(25, ['*'], 'page');

        $items = [];
        foreach ($products as $key => $product) {
            $items[$key]['id'] = $product->id;
            $items[$key]['type'] = 'product';
            $items[$key]['name'] = $product->name;
            $items[$key]['link'] = $product->url;
            $items[$key]['image'] = $product->photo;
            $items[$key]['tags'] = $product->tags;
        }

        $products->appends([$request->input()]);
        $pagination = [
            'current_page' => $products->currentPage(),
            'next_page_url' => $products->nextPageUrl(),
            'last_page' => $products->lastPage(),
            'data' => $items,
        ];

        return response()->json($pagination, Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES);
    }

    public function attachRelation(ProductSelfRelationRequest $request, Product $product)
    {
        $product->productProduct()->detach($request->input('related_product_ids'));
        $product->productProduct()->attach(
            $request->input('related_product_ids'),
            [
                'relationtype_id' => $request->input('relation'),
                'choiceable' => $request->has('choiceable') ? $request->input('choiceable') : 0,
                'required_when' => $request->input('required_when') != 'null' ? $request->input('required_when') : null,
            ],
        );
        return back()->with('success', 'محصولات با موفقیت اضافه شدند');
    }

    public function detachRelation(Request $request, Product $product)
    {
        $request->validate([
            'related_product_id' => ['required', Rule::exists(Product::getTableName(), 'id')],
        ]);
        $product->productProduct()->detach($request->related_product_id);
        return back()->with('success', 'محصول ارتباطی با موفقیت حذف شد ');
    }

    public function sets(Product $product): ResourceCollection
    {
        return ProductSetLiteResource::collection($product->sets()->get());
    }

    public function copy(Product $product)
    {
        $newProduct = $product->replicate();
        $correspondenceArray = [];
        $done = true;
        if (!$newProduct->save()) {
            return response()->json(['message' => 'خطا در کپی از اطلاعات پایه ای محصول . لطفا دوباره اقدام نمایید'],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }
        /**
         * Copying children
         */
        if ($product->hasChildren()) {
            foreach ($product->children as $child) {
                $response = $this->copy($child);
                if ($response->getStatusCode() == Response::HTTP_OK) {
                    $response = json_decode($response->getContent());
                    $newChildId = $response->newProductId;
                    if (isset($newChildId)) {
                        $correspondenceArray[$child->id] = $newChildId;
                        $newProduct->children()
                            ->attach($newChildId);
                    } else {
                        $done = false;
                    }
                } else {
                    $done = false;
                }
            }
        }

        /**
         * Copying attributeValues
         */
        foreach ($product->attributevalues as $attributevalue) {
            $newProduct->attributevalues()
                ->attach($attributevalue->id, [
                    'extraCost' => $attributevalue->pivot->extraCost,
                    'description' => $attributevalue->pivot->description,
                ]);
        }

        /**
         * Copying coupons
         */
        $newProduct->coupons()
            ->attach($product->coupons->pluck('id')
                ->toArray());

        /**
         * Copying complimentary
         */
        foreach ($product->complimentaryproducts as $complimentaryproduct) {
            $flag = $this->haveSameFamily(collect([
                $product,
                $complimentaryproduct,
            ]));
            if (!$flag) {
                $newProduct->complimentaryproducts()
                    ->attach($complimentaryproduct->id);
            }
        }

        /**
         * Copying gifts
         */
        foreach ($product->gifts as $gift) {
            $flag = $this->haveSameFamily(collect([
                $product,
                $gift,
            ]));
            if ($flag) {
                continue;
            }
            $newProduct->gifts()
                ->attach($gift->id, ['relationtype_id' => config('constants.PRODUCT_INTERRELATION_GIFT')]);

        }

        if ($product->hasChildren()) {
            $children = $product->children;
            foreach ($children as $child) {
                $childComplementarities = $child->complimentaryproducts;
                $intersects = $childComplementarities->intersect($children);
                foreach ($intersects as $intersect) {
                    $correspondingChild = Product::where('id', $correspondenceArray[$child->id])
                        ->get()
                        ->first();
                    $correspondingComplimentary = $correspondenceArray[$intersect->id];
                    $correspondingChild->complimentaryproducts()
                        ->attach($correspondingComplimentary);
                }
            }
        }

        if ($done != false) {

            return response()->json([
                'message' => 'عملیات کپی با موفقیت انجام شد.',
                'newProductId' => $newProduct->id,
            ]);
        }
        foreach ($newProduct->children as $child) {
            $child->forceDelete();
        }
        $newProduct->forceDelete();

        return response()->json(['message' => 'خطا در کپی از الجاقی محصول . لطفا دوباره اقدام نمایید'],
            Response::HTTP_SERVICE_UNAVAILABLE);
    }

    public function setDiscount(SetProductDiscountRequest $request)
    {
        $products = $request->get('products');
        if (Product::query()->whereIn('id', $products)->update(['discount' => $request->get('discount')])) {
            Cache::tags(['block', 'product'])->flush();
            return response()->json([
                'message' => 'تخفیف مورد نظر اعمال شد',
            ]);
        }
        return myAbort(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, 'خطای دیتابیس');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditProductRequest|Product  $request
     * @param  Product  $product
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function update(EditProductRequest $request, Product $product)
    {
        $bonId = $request->bon_id;
        $bonPlus = $request->bonPlus;
        $bonDiscount = $request->bonDiscount;
        $childrenPriceEqualizer = $request->has('changeChildrenPrice');

        $this->fillProductFromRequest($request->all(), $product);

        if ($childrenPriceEqualizer) {
            $product->equalizingChildrenPrice();
        }

        if ($bonPlus || $bonDiscount) {
            $this->attachBonToProduct($product, $bonId, $bonDiscount, $bonPlus);
        }

        try {
            $product->update($request->all());
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(new ProductResource($product->refresh()), Response::HTTP_OK);
    }

    public function bulkUpdateStatuses(ProductBulkUpdateStatusesRequest $request)
    {
        $products = Product::whereIn('id', $request->input('product_ids'));
        $products->update($request->only('display', 'enable', 'isFree', 'has_instalment_option'));
        return response()->json([
            'message' => 'product(s) updated successfully'
        ]);
    }
}
