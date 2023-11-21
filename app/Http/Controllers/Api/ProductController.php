<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\ProductSearch;
use App\Collection\ProductCollection;
use App\Events\GetLiveConductor;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddComplimentaryProductRequest;
use App\Http\Requests\EditProductRequest;
use App\Http\Requests\InsertProductRequest;
use App\Http\Requests\ProductConfigRequest;
use App\Http\Requests\ProductContentCommentsRequest;
use App\Http\Requests\ProductContentsRequest;
use App\Http\Requests\UpdateProductAttributeValueRequest;
use App\Http\Resources\Abrisham\AbrishamLessonResource;
use App\Http\Resources\AbrishamContentResource;
use App\Http\Resources\CommentWithContentResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\FlatAbrishamLessonResource;
use App\Http\Resources\LiveProductResource;
use App\Http\Resources\Major as MajorResource;
use App\Http\Resources\Price as PriceResource;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Resources\ProductFaqResource;
use App\Http\Resources\ProductForLanding;
use App\Http\Resources\ProductIndex;
use App\Http\Resources\ProductSetLiteResource;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\Soalaa\SoalaaResource;
use App\Jobs\DanaProductTransferJob;
use App\Jobs\UpdateDanaSessionOrder;
use App\Models\Attributetype;
use App\Models\Attributevalue;
use App\Models\Block;
use App\Models\Conductor;
use App\Models\Content;
use App\Models\Major;
use App\Models\Product;
use App\Models\User;
use App\Repositories\ProductContentsRepository;
use App\Services\DanaProductService;
use App\Traits\APIRequestCommon;
use App\Traits\CharacterCommon;
use App\Traits\ProductCommon;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductController extends Controller
{
    use APIRequestCommon;
    use CharacterCommon;
    use ProductCommon;


    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @param  ProductSearch  $productSearch
     * @return ResourceCollection
     */
    public function index(Request $request, ProductSearch $productSearch)
    {
        $filters = $request->all();
        $filters['seller'] = $request->seller ?? config('constants.ALAA_SELLER');
        $filters['doesntHaveGrand'] = 1;
        $filters['display'] = $request->get('display', 1);

        if ($request->has('length') && $request->get('length') > 0) {
            $productSearch->setNumberOfItemInEachPage($request->get('length'));
        }

        $productResult = $productSearch->get($filters);
        return ProductIndex::collection($productResult);
    }

    public function soalaaProducts()
    {
        $soalaaProducts =
            Product::active()->where('created_at', '>=',
                '2023-07-30')->soalaaProducts(onlyGrand: true)->with('grandsChildren.grandsChildren')->paginate(100);
        return SoalaaResource::collection($soalaaProducts);
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     * @param  Product  $product
     *
     * @return JsonResponse|ProductResource|RedirectResponse|Redirector
     */
    public function showV2(Request $request, Product $product)
    {
        if ($product->id != Product::SHOROO_AZ_NO && isset($product->redirectUrl) && !in_array($product->id,
                [Product::SUBSCRIPTION_12_MONTH, Product::SUBSCRIPTION_3_MONTH, Product::SUBSCRIPTION_1_MONTH])) {
            $redirectUrl = $product->redirectUrl;
            return redirect(convertRedirectUrlToApiVersion($redirectUrl['url'], '2'),
                $redirectUrl['code'], $request->headers->all());
        }

        if (!is_null($product->grandParent)) {
            return redirect($product->grandParent->apiUrl['v1'], Response::HTTP_MOVED_PERMANENTLY,
                $request->headers->all());
        }
        $complimentedProduct = $product->complimentedproducts()->wherePivot('is_dependent', 1)->first();
        if (!is_null($complimentedProduct)) {
            return redirect($complimentedProduct->apiUrl['v1'], Response::HTTP_MOVED_PERMANENTLY,
                $request->headers->all());
        }
        if (!$product->isActive()) {
            return myAbort(Response::HTTP_LOCKED, 'Product is disabled');
        }

        return (new ProductResource($product))->response();
    }

    public function giftProducts(Product $product)
    {
        return ProductIndex::collection($product->gifts);
    }

    //The resource that we need for requests from Abrisham Pro landing page should have the block data and since this data will cause Android application to crash, I cant put this data in ProductResource
    // Therefore I decided to make the following route which is the same but returns another resource
    public function showAjax(Request $request, Product $product)
    {
        if ($product->id != Product::SHOROO_AZ_NO && isset($product->redirectUrl) && !in_array($product->id,
                [Product::SUBSCRIPTION_12_MONTH, Product::SUBSCRIPTION_3_MONTH, Product::SUBSCRIPTION_1_MONTH])) {
            $redirectUrl = $product->redirectUrl;
            return redirect(convertRedirectUrlToApiVersion($redirectUrl['url'], '2'),
                $redirectUrl['code'], $request->headers->all());
        }

        if (!is_null($product->grandParent)) {
            return redirect($product->grandParent->apiUrl['v1'], Response::HTTP_MOVED_PERMANENTLY,
                $request->headers->all());
        }

        if (!$product->isActive()) {
            return myAbort(Response::HTTP_LOCKED, 'Product is disabled');
        }

        return (new ProductForLanding($product))->response();
    }

    /**
     * Store the product
     *
     * @param  InsertProductRequest|Product  $request
     *
     * @return JsonResponse
     * @throws FileNotFoundException
     */

    public function storeV2(InsertProductRequest $request)
    {
        $product = new Product();
        $bonPlus = $request->get('bonPlus');
        $bonDiscount = $request->get('bonDiscount');
        $bonId = $request->get('bon_id');

        $this->fillProductFromRequest($request->all(), $product);

        try {
            $product->save();
            if ($bonPlus || $bonDiscount) {
                $this->attachBonToProduct($product, $bonId, $bonDiscount, $bonPlus);
            }
        } catch (Exception $exception) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $exception->getMessage()],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new ProductResource($product))->response();
    }

    /**
     * Update the product
     *
     * @param  EditProductRequest|Product  $request
     * @param  Product  $product
     *
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function updateV2(EditProductRequest $request, Product $product)
    {
        $bonId = $request->get('bon_id');
        $bonPlus = $request->get('bonPlus');
        $bonDiscount = $request->get('bonDiscount');
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
        return (new ProductResource($product))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product  $product
     * @return Exception|JsonResponse
     * @throws Exception
     */
    public function destroyV2(Product $product)
    {
        try {
            $product->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }
        return response()->json();
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     * @param  Product  $grandProduct
     *
     * @return mixed
     */
    public function refreshPriceV2(Request $request, Product $grandProduct)
    {
        $mainAttributeValues = $request->get('mainAttributeValues');
        $selectedSubProductIds = $request->get('products');
        $extraAttributeValues = $request->get('extraAttributeValues');

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
                            $selectedSubProducts = Product::whereIn('id', $selectedSubProductIds)
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
        $since = $request->get('timestamp');

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

    /**
     * @param  Request  $request
     * @return ResourceCollection
     */
    public function abrishamLessons(Request $request)
    {
        $isPro = $request->get('isPro', 0);
        $userMajorCategory = -1;
        if (
            isset($request->user()->major_id) &&
            in_array($request->user()->major_id, [Major::RIYAZI, Major::TAJROBI])
        ) {
            $userMajorCategory = $request->user()->major_id;
        }

        switch ($isPro) {
            case 0 :
                $abrishamCategory = Product::ABRISHAM_PRODUCTS_CATEGORY;
                $abrishamLessonsInfo = Product::ALL_ABRISHAM_PRODUCTS;
                break;
            case 1:
                $abrishamCategory = Product::ABRISHAM_PRO_PRODUCTS_CATEGORY;
                $abrishamLessonsInfo = Product::ALL_ABRISHAM_PRO_PRODUCTS;
                break;
        }

        $categories = [];
        foreach ($abrishamCategory as $category) {
            $lessons = [];
            $counter = 0;
            foreach ($category['products'] as $productId) {
                $lessonInfo = Arr::get($abrishamLessonsInfo, $productId);
                $lessons[] = [
                    'id' => $productId,
                    'title' => Arr::get($lessonInfo, 'lesson_name'),
                    'color' => Arr::get($lessonInfo, 'color'),
                    'selected' => $counter++ == 0 && $userMajorCategory == $category['user_major_category'],
                ];
            }
            $categories[] = [
                'title' => $category['title'],
                'lessons' => $lessons,
            ];
        }

        return AbrishamLessonResource::collection($categories);
    }

    public function taftanLessons(Request $request)
    {
        $userMajorCategory = -1;
        if (
            isset($request->user()->major_id) &&
            in_array($request->user()->major_id, [Major::RIYAZI, Major::TAJROBI])
        ) {
            $userMajorCategory = $request->user()->major_id;
        }

        $categories = [];
        foreach (Product::TAFTAN_PRODUCTS_CATEGORY as $category) {
            $lessons = [];
            $counter = 0;
            foreach ($category['products'] as $productId) {
                $lessonInfo = Arr::get(Product::ALL_TAFTAN_PRODUCTS, $productId);
                $lessons[] = [
                    'id' => $productId,
                    'title' => Arr::get($lessonInfo, 'lesson_name'),
                    'color' => Arr::get($lessonInfo, 'color'),
                    'selected' => $counter++ == 0 && $userMajorCategory == $category['user_major_category'],
                ];
            }
            $categories[] = [
                'title' => $category['title'],
                'lessons' => $lessons,
            ];
        }

        return AbrishamLessonResource::collection($categories);
    }

    public function flatLessons(Request $request)
    {
        $lessons = [];
        foreach (Product::ABRISHAM_PRODUCTS_CATEGORY as $category) {
            foreach ($category['products'] as $productId) {
                $lessonInfo = Arr::get(Product::ALL_ABRISHAM_PRODUCTS, $productId);

                $lessons[$productId] = [
                    'id' => $productId,
                    'title' => Arr::get($lessonInfo, 'lesson_name'),
                ];
            }
        }

        return FlatAbrishamLessonResource::collection($lessons);
    }

    /**
     * @param  Request  $request
     * @param  Product  $product
     *
     * @return JsonResponse
     */
    public function nextWatchContent(Request $request, Product $product)
    {
        $content = $this->cachedNextWatchContent($request->user(), $product);

        if (is_null($content)) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'محصول هیچ محتوای فعالی ندارد!');
        }

        return (new AbrishamContentResource($content))->response();
    }

    /**
     * @return ResourceCollection
     */
    public function abrishamMajors()
    {
        $majors = Major::query()->whereIn('id', [Major::RIYAZI, Major::TAJROBI])->get();
        return MajorResource::collection($majors);
    }

    public function chatrNejatMajors()
    {
        $majors = Major::query()->whereIn('id', [Major::RIYAZI, Major::TAJROBI])->get();
        return MajorResource::collection($majors);
    }

    /**
     * @return ResourceCollection
     */
    public function taftanMajors()
    {
        $majors = Major::query()->whereIn('id', [Major::RIYAZI, Major::TAJROBI])->get();
        return MajorResource::collection($majors);
    }

    public function productCategory()
    {
        $categoryArray = [
            [
                'name' => 'همه',
                'value' => 'all',
                'selected' => true,
            ],
            [
                'name' => 'راه ابریشم',
                'value' => 'VIP',
                'selected' => false,
            ],
            [
                'name' => '110',
                'value' => 'VIP110',
                'selected' => false,
            ],
            [
                'name' => 'آرش',
                'value' => 'همایش/آرش',
                'selected' => false,
            ],
            [
                'name' => 'تایتان',
                'value' => 'همایش/تایتان',
                'selected' => false,
            ],
            [
                'name' => 'تفتان',
                'value' => 'همایش/تفتان',
                'selected' => false,
            ],
            [
                'name' => 'تتا',
                'value' => 'همایش/تتا',
                'selected' => false,
            ],
            [
                'name' => 'گدار',
                'value' => 'همایش/گدار',
                'selected' => false,
            ],
            [
                'name' => 'جزوه',
                'value' => 'جزوه',
                'selected' => false,
            ],
            [
                'name' => 'آزمون',
                'value' => 'آزمون/سه آ',
                'selected' => false,
            ],
        ];
        return ProductCategoryResource::collection($categoryArray);
    }

    public function userProducts(Request $request)
    {
        $user = $request->user();
        return ProductResource::collection($user->productWithParams($request));
    }

    public function sampleVideo(Product $product)
    {
        $contentsId = $product->sample_contents?->tags;
        $contents = $contentsId ? Content::whereIn('id', $contentsId)->get() : collect();
        return \App\Http\Resources\Content::collection($contents);
    }

    public function contents(ProductContentsRequest $request, Product $product)
    {
        $productContents = ProductContentsRepository::productInitQuery();
        $productContents = ProductContentsRepository::contents(
            $productContents,
            $product->id,
            $request->get('type', []),
            $request->get('limit'),
            $request->get('search'),
            $request->get('contentset_title')
        );
        return \App\Http\Resources\Content::collection($productContents);
    }

    public function contentComments(ProductContentCommentsRequest $request, Product $product)
    {
        $productContentComments = ProductContentsRepository::productInitQuery();
        $productContentComments = ProductContentsRepository::comments(
            $productContentComments,
            $request->user(),
            $product->id,
            [1, 8],
            $request->get('search'),
            $request->get('contentset_title'),
            $request->get('created_at_since'),
            $request->get('created_at_till'),
            $request->get('limit', 15)
        );
        return CommentWithContentResource::collection($productContentComments);
    }

    public function chatreNejatLessons(Request $request)
    {
        $userMajorCategory = -1;
        if (
            isset($request->user()->major_id) &&
            in_array($request->user()->major_id, [Major::RIYAZI, Major::TAJROBI])
        ) {
            $userMajorCategory = $request->user()->major_id;
        }

        $abrishamCategory = Product::CHATR_NEJAT2_PRODUCTS_CATEGORY;
        $abrishamLessonsInfo = Product::ALL_CHATR_NEJAT2_PRODUCTS;

        $categories = [];
        foreach ($abrishamCategory as $category) {
            $lessons = [];
            $counter = 0;
            foreach ($category['products'] as $productId) {
                $lessonInfo = Arr::get($abrishamLessonsInfo, $productId);
                $lessons[] = [
                    'id' => $productId,
                    'title' => Arr::get($lessonInfo, 'lesson_name'),
                    'color' => Arr::get($lessonInfo, 'color'),
                    'selected' => $counter++ == 0 && $userMajorCategory == $category['user_major_category'],
                ];
            }
            $categories[] = [
                'title' => $category['title'],
                'lessons' => $lessons,
            ];
        }

        return AbrishamLessonResource::collection($categories);
    }

    public function lives()
    {
        $lives = Product::join('liveconductors as po', 'po.product_id', '=', 'products.id')
            ->orderBy('po.date')
            ->orderBy('po.start_time')
            ->where('po.date', '>', '2023-07-14')
            ->select('products.*')->get();
        $lives = $lives->unique('id');

        return LiveProductResource::collection($lives);
    }

    public function liveInfo(Request $request, Product $product)
    {
        //Initiation
        $skyroomIds = [
            1093 => 3801819,
            1100 => 3801814,
            1090 => 3801814,
            1092 => 3771248,
            1101 => 3771248,
            1098 => 3801786,
            1095 => 3801734,
            1099 => 3771249,
            1094 => 3771246,
            1091 => 3771243,
        ];
        $user = $request->user();
        $userFullName = null;
        $firstName = $user->firstName;
        $lastName = $user->lastName;
        $roomId = Arr::get($skyroomIds, $product->id);
        $liveConductor =
            Conductor::where('date', Carbon::today()->toDateString())
                ->where('start_time', '<', now()->setTimezone('Asia/Tehran'))
                ->where('finish_time', '>', now()->setTimezone('Asia/Tehran'))
                ->where('product_id', $product->id)->first();

        $nonAbrishamClassSeatCapacity = 0;
        $abrishamClassSeatCapacity = 130;


        //main
        if (!isset($roomId)) {
            return myAbort(Response::HTTP_LOCKED, 'کلاس زنده ای برای این محصولی وجود ندارد');
        }

        if (!isset($liveConductor)) {
            return myAbort(Response::HTTP_LOCKED, 'کلاسی برای این محصول در این ساعت وجود ندارد');
        }

        $isAbrishamStudent = $user->orders()->whereOrderstatusId(config('constants.ORDER_STATUS_CLOSED'))
            ->whereIn('paymentstatus_id', [
                config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
                config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'), config('constants.PAYMENT_STATUS_INDEBTED')
            ])
            ->whereHas('orderproducts', function ($q2) {
                $q2->whereIn('product_id',
                    [1101, 1099, 1095, 1094, 1091, 1090, 1100, 1095, 1094, 1093, 1092, 1098, 1097, 1096]);
            })->count();

        $allStudentsInClass =
            User::distinct()->whereHas('liveConductors', function ($q) use ($liveConductor) {
                $q->where('live_conductor_id', $liveConductor->id);
            })->count();
        $abrishamStudentsInClass =
            User::distinct()->whereHas('liveConductors', function ($q) use ($liveConductor) {
                $q->where('live_conductor_id', $liveConductor->id);
            })->whereHas('orders', function ($q) {
                $q->whereOrderstatusId(config('constants.ORDER_STATUS_CLOSED'))
                    ->whereIn('paymentstatus_id', [
                        config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
                        config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
                        config('constants.PAYMENT_STATUS_INDEBTED')
                    ])
                    ->whereHas('orderproducts', function ($q2) {
                        $q2->whereIn('product_id',
                            [1101, 1099, 1095, 1094, 1091, 1090, 1100, 1095, 1094, 1093, 1092, 1098, 1097, 1096]);
                    });
            })->count();
        $remainedNonAbrishamClassCapacity =
            $nonAbrishamClassSeatCapacity - ($allStudentsInClass - $abrishamStudentsInClass);

        if (!$isAbrishamStudent && $remainedNonAbrishamClassCapacity <= 0) {
            return myAbort(Response::HTTP_LOCKED, 'ظرفیت کلاس تکمیل شده است');
        }
        $remainedAbrishamClassCapacity = $abrishamClassSeatCapacity - $abrishamStudentsInClass;
        if ($isAbrishamStudent && $abrishamStudentsInClass >= 2 * $abrishamClassSeatCapacity) {
            return myAbort(Response::HTTP_LOCKED, 'ظرفیت کلاس تکمیل شده است');
        }


//        if (!$product->is_ordered) {
//            return response()->json([
//                'data' =>
//                    [
//                        'live_link' => null,
//                    ],
//            ]);
//        }

        GetLiveConductor::dispatch($liveConductor, $user->id);

        if ($abrishamStudentsInClass > $abrishamClassSeatCapacity) {
            return response()->json([
                'data' =>
                    [
                        'live_link' => $liveConductor->description,
                    ],
            ]);
        }


        return response()->json([
            'data' =>
                [
                    'live_link' => $liveConductor->live_link,
                ],
        ]);


        if (isset($firstName) && strlen($firstName) > 0) {
            $userFullName = $firstName;
        }
        if (isset($lastName) && strlen($lastName) > 0) {
            if (isset($userFullName)) {
                $userFullName .= ' '.$lastName;
            } else {
                $userFullName = $lastName;
            }
        }

        $nickName = (isset($userFullName)) && strlen($userFullName) > 2 ? $userFullName : 'دانش آموز آلاء';
        $dto = [
            'action' => 'createLoginUrl',
            'params' => [
                'room_id' => $roomId,
                'user_id' => $user->id,
                'nickname' => $nickName,
                'access' => 1,
                'concurrent' => 1,
                'language' => 'en',
                'ttl' => 3600,
            ],
        ];


        $result = $this->postToSkyroom(json_encode($dto));
        if (!$result['result']) {
            Log::error('ProductController@liveInfo : Error on connecting to Skyroom user-'.$user->id.' , result: '.$result['result']);
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'فعلا سرور اسکای روم از دسترسی خارج شده! :(');
        }

        $resultData = Arr::get($result, 'data');
        return response()->json([
            'data' =>
                [
                    'live_link' => Arr::get($resultData, 'result'),
                ],
        ]);
    }

    public function faq(Product $product)
    {
        return ProductFaqResource::collection($product->faqs);
    }

    public function complimentary(Product $product)
    {
        $product->complimentaryproducts->each(function ($complimentaryProduct) {
            $complimentaryProduct->setAttribute('is_dependent', $complimentaryProduct->pivot->is_dependent);
        });
        return ProductIndex::collection($product->complimentaryproducts);
    }

    public function exams(Product $product)
    {
        return ExamResource::collection($product->productExams);
    }

    public function updateSetOrder(Request $request, Product $product)
    {
        Validator::make($request->all(), [
            'product_orders' => ['required', 'array'],
        ])->validate();

        $orders = $request->get('product_orders');
        foreach ($orders as $order) {
            $product->sets()->updateExistingPivot($order['id'], ['order' => $order['order']]);
        }

        // TODO: I have to search in internet to find out why only the first method that uses the tags works properly but
        //  the second method that uses the key doesn't work.!!!
        Cache::tags(['product', 'set', 'product_'.$product->id, 'product_'.$product->id.'_sets', 'userAsset'])->flush();
//        $key = 'product:sets:' . (new Product())->cacheKey();
//        Cache::forget($key);

        UpdateDanaSessionOrder::dispatch($product->id, $orders);
        return response()->json();
    }

    /**
     * @param  Product  $product
     * @return ResourceCollection
     */
    public function sets(Product $product): ResourceCollection
    {
        return ProductSetLiteResource::collection($product->sets);
    }

    public function transferToDana(Request $request, Product $product)
    {
        if (!$product->enable) {
            session()->flash('error', 'نمی توانید محصول غیرفعال را منتقل کنید');
            return redirect()->back();

        }
        if (!is_null($product->redirectUrl)) {
            session()->flash('error', 'نمی توانید محصول ریدایرکت شده را منتقل کنید');
            return redirect()->back();

        }

        $danaProductId = DanaProductService::createCourse($product);
        if ($danaProductId && $request->query('withSet')) {
            foreach ($product->sets as $set) {
                if (str_contains($set->name, 'مشاوره')) {
                    continue;
                }

                DanaProductTransferJob::dispatch($danaProductId, $set, $set->getOriginal('pivot_order'), $product->id);
            }
        }

        return response()->json(['success' => 'Transfer Successfully'], 200);
    }

    public function createConfiguration(Product $product)
    {
        $attributeCollection = collect();
        $attributeGroups = $product->attributeset->attributeGroups;
        foreach ($attributeGroups as $attributeGroup) {
            $attributeType = Attributetype::where('name', 'main')
                ->get()
                ->first();
            $attributes = $product->attributeset->attributes()
                ->where('attributetype_id', $attributeType->id);
            foreach ($attributes as $attribute) {
                $attributeValues = $attribute->attributevalues;
                $attributeValuesCollect = collect();
                foreach ($attributeValues as $attributeValue) {
                    $attributeValuesCollect->push($attributeValue);
                    //                        array_push($attributeValuesArray , $attributeValue);
                }
                $attributeCollection->push([
                    'attribute' => $attribute,
                    'attributeControl' => $attribute->attributecontrol->name,
                    'attributevalues' => $attributeValuesCollect,
                ]);
            }
        }
        return response()->json(compact('product', 'attributeCollection'));
    }

    public function updateProductsConfig(ProductConfigRequest $request)
    {
        $products = Product::query();
        if (!in_array(0, $request->get('products'))) {
            $products->whereIn('id', $request->get('products'));
        }

        $message = $products->update($request->only(['enable', 'display'])) ?
            'محصولات اتخاب شده با موفقیت پیکربندی شدند' :
            'پیکربندی محصولات با مشکل مواجه شد.';

        return response()->json($message);
    }

    public function makeConfiguration(Request $request, $product)
    {

        $matrix = [];
        $array = []; // checkbox attribute values

        $attributeIds = $request->get('attributevalues');
        $extraCosts = $request->get('extraCost');
        $orders = $request->get('order');
        $descriptions = $request->get('description');
        $i = 0;
        foreach ($attributeIds as $attributeId) {
            $j = 0;
            foreach ($attributeId as $attributevalueId) {
                $extraCost = $extraCosts[$attributevalueId];
                if (!isset($extraCost[0])) {
                    $extraCost = 0;
                }

                $order = $orders[$attributevalueId];
                if (!isset($order[0])) {
                    $order = 0;
                }

                $description = $descriptions[$attributevalueId];
                if (!isset($description[0])) {
                    $description = null;
                }

                $attributevalue = Attributevalue::findOrFail($attributevalueId);
                $product->attributevalues()
                    ->attach($attributevalue, [
                        'extraCost' => $extraCost,
                        'order' => $order,
                        'description' => $description,
                    ]);
                if (strcmp($attributevalue->attribute->attributecontrol->name, 'groupedCheckbox') == 0) {
                    $array[] = $attributevalue->id;
                } else {
                    $matrix[$i][$j] = $attributevalue->id;
                    $j++;
                }
            }
            $i++;
        }

        if (count($matrix) == 0) {
            return redirect()->back();
        }
        if (count($matrix) == 1) {
            $productConfigurations = current($matrix);
        } elseif (count($matrix) >= 2) {
            $vertex = array_pop($matrix);
            $productConfigurations = $this->cartesianProduct($matrix, $vertex)[0];
        }

        foreach ($array as $item) {
            foreach ($productConfigurations as $productConfig) {
                $newProductConfig = $productConfig.','.$item;
                $productConfigurations[] = $newProductConfig;
            }
        }

        foreach ($productConfigurations as $productConfig) {
            $childProduct = $product->replicate();
            $childProduct->order = 0;
            $attributevalueIds = explode(',', $productConfig);
            $productName = '';
            $attributevalues = [];
            foreach ($attributevalueIds as $attributevalueId) {
                $attributevalue = Attributevalue::findOrFail($attributevalueId);
                $attributevalues[] = $attributevalue;
                $productName = $productName.'-'.$attributevalue->name;
            }
            $childProduct->name = $product->name.$productName;
            $childProduct->producttype_id = 1;
            if ($childProduct->save()) {
                $childProduct->parents()
                    ->attach($product);
                foreach ($attributevalues as $attributevalue) {

                    $extraCost = $extraCosts[$attributevalue->id];
                    if (!isset($extraCost[0])) {
                        $extraCost = 0;
                    }

                    $order = $orders[$attributevalue->id];
                    if (!isset($order[0])) {
                        $order = 0;
                    }

                    $description = $descriptions[$attributevalue->id];
                    if (!isset($description[0])) {
                        $description = null;
                    }

                    $childProduct->attributevalues()
                        ->attach($attributevalue, [
                            'extraCost' => $extraCost,
                            'order' => $order,
                            'description' => $description,
                        ]);
                }
            } else {
                session()->put('error', 'خطای پایگاه داده');
            }
        }
        return response()->json(action($product));
    }

    public function editAttributevalues(Product $product)
    {
        $attributeValuesCollection = collect();

        $attributeset = $product->attributeset;
        $attributeGroups = $attributeset->attributegroups;
        foreach ($attributeGroups as $attributeGroup) {
            $attributes = $attributeGroup->attributes->sortBy('order');

            // TODO: There is a bug here that seems from database data. That is, duplicate attributes are received
            //  from the database. For this reason, duplicate attributevalues are also displayed in the panel. Duplicate
            //  attributevalues eventually affect the updateAttributevalues method and generate bugs. During the
            //  last decision that was made with Mr. Shahrokhi, bug fixes for this part have been postponed.
            foreach ($attributes as $attribute) {
                $type = Attributetype::FindOrFail($attribute->attributetype_id);
                $productAttributeValues = $product->attributevalues->where('attribute_id', $attribute->id);

                $productAttributeValuesClone = clone $productAttributeValues;
                $attrributevalues = [];
                foreach ($attribute->attributevalues as $attributevalue) {
                    $attributevalue['isMember'] = in_array($attributevalue->id,
                        $productAttributeValuesClone->pluck('id')->toArray());
                    $attrributevalues[] = $attributevalue;
                }

                if (!isset($attributeValuesCollection[$type->id])) {
                    $attributeValuesCollection->put($type->id, collect([
                        'name' => $type->name,
                        'displayName' => $type->description,
                        'attributes' => [],
                    ]));
                }
                $helperCollection = collect($attributeValuesCollection[$type->id]['attributes']);

                $productAttributeValuesWithKey = [];
                foreach ($productAttributeValues as $productAttributeValue) {
                    $productAttributeValuesWithKey[$productAttributeValue->id] = $productAttributeValue;
                }

                $helperCollection->push([
                    'name' => $attribute->displayName,
                    'type' => $type,
                    'values' => $attrributevalues,
                    'productAttributevalues' => $productAttributeValues,
                    'productAttributeValuesWithKey' => $productAttributeValuesWithKey,
                ]);
                $attributeValuesCollection[$type->id]->put('attributes', $helperCollection);
            }
        }
        return response()->json(compact('product', 'attributeValuesCollection'));
    }

    public function updateAttributevalues(UpdateProductAttributeValueRequest $request, Product $product)
    {
        $product->attributevalues()->detach($product->attributevalues->pluck('id')->toArray());

        $newExtraCost = $request->get('extraCost');
        $newDescription = $request->get('description');

        $attachedAttributeValues = [];
        foreach ($request->attributevalues as $attributevalueId) {

            $extraCost = $newExtraCost[$attributevalueId];
            if (strlen($extraCost) == 0) {
                $extraCost = null;
            }

            $description = $newDescription[$attributevalueId];
            if (strlen($description) == 0) {
                $description = null;
            }

            // TODO: This is a temporary solution to the problem of duplicate attributevalues displayed for the
            //  "edit product attributevalues" panel. After solving that, you can delete this solution if you wish.
            if (in_array($attributevalueId, $attachedAttributeValues)) {
                continue;
            }
            if (!$product->attributevalues()->attach($attributevalueId,
                ['extraCost' => $extraCost, 'description' => $description])) {
                // Register attached attributeValues
                $attachedAttributeValues[] = $attributevalueId;
                continue;
            }

            $this->updateProductChildrenAttributeValueExistingPivot($product, $attributevalueId, $extraCost,
                $description);

        }

        Cache::tags(['product_'.$product->id])->flush();

        return response()->json(action($product));
    }

    public function attachAttributeValue(AttachProductAttributeValueRequest $request, Product $product)
    {
        $response = [];

        $productAttributes = $product->attributeset->attributes();

        if (!$productAttributes->pluck('id')->contains($request->attribute_id)) {
            $response['error'] = 'Invalid product attribute ID!';
            return response()->json($response, 400);
        }

        $productAttributeAttributeValues = $productAttributes->find($request->attribute_id)->attributevalues;

        if (!$productAttributeAttributeValues->contains($request->attribute_value_id)) {
            $response['error'] = 'Invalid product attribute value ID!';
            return response()->json($response, 400);
        }

        try {
            $product->attributevalues()->attach($request->attribute_value_id, [
                'order' => $request->order,
                'extraCost' => $request->extra_cost,
                'description' => $request->description,
            ]);

            $this->updateProductChildrenAttributeValueExistingPivot($product, $request->attribute_value_id,
                $request->extra_cost, $request->description, $request->order);
            Cache::tags(['product_'.$product->id])->flush();

            $response['success'] = 'افزودن مقدار صفت به محصول با موفقیت انجام شد';
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response['error'] = 'افزودن مقدار صفت به محصول با خطا مواجه شده است!';
            return response()->json($response, 500);
        }
    }

    public function detachAttributeValue(
        Request $request,
        Product $product,
        Attributevalue $attributeValue
    ): JsonResponse {
        try {
            $product->attributevalues()->detach($attributeValue);
            Cache::tags(['product_'.$product->id])->flush();
        } catch (Exception $e) {
            return response()->json(['message', 'حذف مقدار صفت محصول با خطا مواجه شده است!', 'errorInfo' => $e],
                ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
        }
        return response()->json([], ResponseAlias::HTTP_OK);
    }

    private function updateProductChildrenAttributeValueExistingPivot(
        Product $product,
        $attributeValue,
        int $extraCost,
        string $description = null,
        int $order = 0
    ): bool {
        $attributeValue = $attributeValue instanceof Attributevalue ? $attributeValue : Attributevalue::find($attributeValue);
        try {
            $children = $product->children()
                ->whereHas('attributevalues', function ($q) use ($attributeValue) {
                    $q->where('id', $attributeValue->id);
                })
                ->get();

            foreach ($children as $child) {
                $child->attributevalues()
                    ->where('id', $attributeValue->id)
                    ->updateExistingPivot($attributeValue->id, [
                        'order' => $order,
                        'extraCost' => $extraCost,
                        'description' => $description,
                    ]);
            }
        } catch (Exception $exception) {
            return false;
        }
        return true;
    }

    public function addGift(Request $request, Product $product)
    {
        $response = [];

        $gift = Product::findOrFail($request->get('giftProducts'));

        if ($product->gifts->contains($gift)) {
            $response['error'] = 'این هدیه قبلا به این محصول اضافه شده است';
            return response()->json($response, 400);
        } else {
            $product->gifts()->attach($gift, ['relationtype_id' => config('constants.PRODUCT_INTERRELATION_GIFT')]);
            $response['success'] = 'هدیه با موفقیت به محصول اضافه شد';
            return response()->json($response, 200);
        }
    }

    public function removeGift(Request $request, Product $product)
    {
        $gift = Product::where('id', $request->get('giftId'))
            ->get()
            ->first();
        if (!isset($gift)) {
            return response()->json(['message' => 'خطا! چنین محصول هدیه ای وجود ندارد'],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if ($product->gifts()
            ->detach($gift->id)) {
            return response()->json(['message' => 'هدیه با موفقیت حذف شد']);
        }
        return response()->json(['message' => 'خطا در حذف هدیه . لطفا دوباره اقدام نمایید'],
            Response::HTTP_SERVICE_UNAVAILABLE);
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
         * Copying bons
         */
        foreach ($product->bons as $bon) {
            $newProduct->bons()
                ->attach($bon->id, [
                    'discount' => $bon->pivot->discount,
                    'bonPlus' => $bon->pivot->bonPlus,
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
            ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }

    public function attachBlock(Request $request, Product $product)
    {
        $response = [];

        $block = Block::find($request->get('block_id'));
        if (is_null($block)) {
            $response['error'] = 'Block not found';
            return response()->json($response, 404);
        }

        $product->blocks()->attach($block->id);

        $contentsIds = $this->getProductsSampleContentsFromBlock($block);
        $productSampleContents = optional($product->sample_contents)->tags;
        if (!is_null($productSampleContents)) {
            $contentsIds = array_values(array_unique(array_merge($contentsIds, $productSampleContents), SORT_REGULAR));
        }

        if (!empty($contentsIds)) {
            $product->sample_contents = $contentsIds;
            $product->update();
        }

        Cache::tags(['product_'.$product->id])->flush();

        $response['success'] = 'بلاک با موفقیت اضافه شد';
        return response()->json($response, 200);
    }

    public function detachBlock(Request $request, Product $product)
    {
        $response = [];

        $block = Block::find($request->get('block_id'));
        if (is_null($block)) {
            $response['error'] = 'Block not found';
            return response()->json($response, 404);
        }

        $product->blocks()->detach($block->id);

        $contentsIds = $this->getProductsSampleContentsFromBlock($block);
        $productSampleContents = optional($product->sample_contents)->tags;
        if (!is_null($productSampleContents)) {
            $contentsIds = array_values(array_unique(array_diff($productSampleContents, $contentsIds), SORT_REGULAR));
        }

        $product->sample_contents = $contentsIds;
        $product->update();

        Cache::tags(['product_'.$product->id])->flush();

        $response['success'] = 'Block successfully detached';
        return response()->json($response, 200);
    }

    public function childProductEnable(Request $request, $product)
    {
        $response = [];

        $parent = $product->parents->first();
        if ($product->enable == 1) {
            $product->enable = 0;
            foreach ($product->attributevalues as $attributevalue) {
                $flag = 0;
                $children = $parent->children->where('id', '!=', $product->id)->where('enable', 1);
                foreach ($children as $child) {
                    if ($child->attributevalues->contains($attributevalue) == true) {
                        $flag = 1;
                        break;
                    }
                }
                if ($flag == 0) {
                    $parent->attributevalues()->detach($attributevalue);
                }
            }
        } elseif ($product->enable == 0) {
            $product->enable = 1;
            foreach ($product->attributevalues as $attributevalue) {
                if ($parent->attributevalues->contains($attributevalue) != false) {
                    continue;
                }
                if (isset($attributevalue->pivot->extraCost) && $attributevalue->pivot->extraCost > 0) {
                    $attributevalueDescription = '+'.number_format($attributevalue->pivot->extraCost).'تومان';
                } else {
                    $attributevalueDescription = null;
                }

                $parent->attributevalues()->attach($attributevalue->id,
                    ['description' => $attributevalueDescription]);
            }
        }
        if ($product->update()) {
            Cache::tags(['product_'.$product->id, 'product_'.$parent->id])->flush();
            $response['success'] = 'وضعیت فرزند محصول با موفقیت تغییر کرد';
            return response()->json($response, 200);
        }

        $response['error'] = 'خطای پایگاه داده';
        return response()->json($response, 500);
    }

    public function addComplimentary(AddComplimentaryProductRequest $request, Product $product)
    {
        $response = [];

        $complimentary = Product::findOrFail($request->get('complimentaryproducts'));

        if ($product->complimentaryproducts->contains($complimentary)) {
            $response['error'] = 'این اشانتیون قبلا درج شده است';
        } else {
            $product->complimentaryproducts()->attach($complimentary);
            $response['success'] = 'درج اشانتیون با موفقیت انجام شد';
        }

        return response()->json($response, 200);
    }

    public function removeComplimentary(Request $request, Product $complimentary)
    {
        $product = Product::findOrFail($request->get('productId'));
        $product->complimentaryproducts()
            ->detach($complimentary);
        session()->put('success', 'حذف اشانتیون با موفقیت انجام شد');

        return response()->json();
    }

}
