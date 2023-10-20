<?php

namespace App\Services;

use App\Classes\OrderProduct\RefinementProduct\RefinementFactory;
use App\Collection\OrderproductCollection;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderproductRepo;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class OrderProductsService
{
    public function __construct(public OrderproductRepo $orderproductRepo)
    {
    }

    public static function convertOrderproductObjectsToCollection(array $cookieOrderproducts): OrderproductCollection
    {
        $fakeOrderproducts = new OrderproductCollection();
        foreach ($cookieOrderproducts as $key => $cookieOrderproduct) {
            $grandParentProductId = optional($cookieOrderproduct)->product_id;
            $childrenIds = optional($cookieOrderproduct)->products;
            $attributes = optional($cookieOrderproduct)->attribute;
            $extraAttributes = optional($cookieOrderproduct)->extraAttribute;
            $grandParentProduct = Product::Find($grandParentProductId);
            if (!isset($grandParentProduct)) {
                continue;
            }

            $data = [
                'products' => $childrenIds,
                'atttibute' => $attributes,
                'extraAttribute' => $extraAttributes,
            ];

            $products = (new RefinementFactory($grandParentProduct, $data))->getRefinementClass()
                ->getProducts();

            /** @var Product $product */
            foreach ($products as $product) {
                $fakeOrderproduct = new Orderproduct();
                $fakeOrderproduct->id = $product->id;
                $fakeOrderproduct->product_id = $product->id;
                $costInfo = $product->calculatePayablePrice();
                $fakeOrderproduct->cost = $costInfo['cost'];
                $fakeOrderproduct->updated_at = Carbon::now();
                $fakeOrderproduct->created_at = Carbon::now();

                $fakeOrderproducts->push($fakeOrderproduct);
            }
        }

        return $fakeOrderproducts;
    }

    public static function mapRelatedProductForAddToOrderProduct($relatedProducts, $orderProductPack, $order)
    {
        $orderProductTransformer = $orderProductPack->product->transformer;
        $isTransformer = $orderProductTransformer->isNotEmpty();

        $filterRelatedProducts = $relatedProducts->filter(function ($value, $key) {
            return $value->pivot->choiceable != 1;
        });

        if ($isTransformer && $newChosenProduct = OrderProductsService::previouslyChosenProduct($relatedProducts,
                $orderProductTransformer, $order->user)) {
            $filterRelatedProducts->push($newChosenProduct);
            $filterRelatedProducts = $filterRelatedProducts->flatten();
        }

        $itemsNotAdded = $filterRelatedProducts->pluck('id')
            ->diff($order->orderproducts()->get()->pluck('product_id'));

        $RelatedProductsThatMustBeAdded = $filterRelatedProducts->whereIn('id', $itemsNotAdded);

        ProductService::AddRelatedProductToOrderProduct($RelatedProductsThatMustBeAdded, $orderProductPack,
            $order->id,);

        $filterRelatedProducts->each(function ($relatedProduct) use ($orderProductPack, $order) {
            self::mapRelatedProductForAddToOrderProduct($relatedProduct->itemAndGift, $orderProductPack, $order);
        });
    }

    public static function previouslyChosenProduct($relatedProducts, $orderProductTransformer, $user)
    {
        $choiceAbleProducts = $relatedProducts->filter(function ($value, $key) {
            return $value->pivot->choiceable == 1;
        });

        if ($choiceAbleProducts->isNotEmpty()) {
            //products that might user chosen in the past
            $productsMightChosenInPast = $choiceAbleProducts->pluck('pivot.required_when');
            $orderChosenProduct =
                $user->orders()->whereNotIn('paymentstatus_id',
                    [config('constants.PAYMENT_STATUS_UNPAID')])->with('orderproducts')
                    ->whereHas('orderproducts',
                        function ($q) use ($orderProductTransformer, $productsMightChosenInPast) {
                            $q->whereIn('product_id', $orderProductTransformer->pluck('id'));
                        })->whereHas('orderproducts',
                        function ($q) use ($orderProductTransformer, $productsMightChosenInPast) {
                            $q->whereIn('product_id', $productsMightChosenInPast);
                        })
                    ->get();

            $orderproducts = $orderChosenProduct->pluck('orderproducts');
            if (!isset($orderChosenProduct) || $orderproducts->isEmpty()) {
                return null;
            }
            $chosenProducts = $orderproducts->flatten()->whereIn('product_id',
                $productsMightChosenInPast)->pluck('product');

            return $choiceAbleProducts->filter(function ($value) use ($chosenProducts) {
                return $chosenProducts->pluck('id')->contains($value->pivot->required_when);
            });
        }
        return null;
    }

    public function destroyOrderProduct(Orderproduct $orderproduct)
    {
        /** @var User $user */
        $user = auth()->user();
        $orderproduct_userbons = $orderproduct->userbons;
        foreach ($orderproduct_userbons as $orderproduct_userbon) {
            $orderproduct_userbon->usedNumber =
                $orderproduct_userbon->usedNumber - $orderproduct_userbon->pivot->usageNumber;
            $orderproduct_userbon->userbonstatus_id = config('constants.USERBON_STATUS_ACTIVE');
            if ($orderproduct_userbon->usedNumber >= 0) {
                $orderproduct_userbon->update();
            }
        }
        try {
            $deleteFlag = $orderproduct->delete() ? true : false;
        } catch (Exception $e) {
            Log::error('OrderproductController:destroy:294:deleting orderproduct');
        }

        $previousRoute = app('router')
            ->getRoutes()
            ->match(app('request')->create(URL::previous()))
            ->getName();
        if (strcmp($previousRoute, 'order.edit') == 0) {
            $orderproduct->updateOrderCost();
        }

        if (!$deleteFlag) {
            return response()->json(['message' => 'خطا در حذف محصول سفارش'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $user->unUsedSubscription($orderproduct);

        $orderproduct->children->each(function ($child, $key) {
            $child->delete();
        });

        Cache::tags([
            'order_'.$orderproduct->order_id.'_products',
            'order_'.$orderproduct->order_id.'_orderproducts',
            'order_'.$orderproduct->order_id.'_cost',
            'order_'.$orderproduct->order_id.'_bon',
        ])->flush();

        return response()->json(['message' => 'محصول سفارش با موفقیت حذف شد!']);
    }
}
