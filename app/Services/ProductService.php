<?php

namespace App\Services;

use App\Repositories\OrderproductRepo;
use Illuminate\Support\Arr;

class ProductService
{
    public static function AddRelatedProductToOrderProduct($products, $orderProduct, $order_id)
    {
        $products->each(function ($product) use ($orderProduct, $order_id) {
            $price = Arr::get($product->price, 'base', 0);
            switch ($product->pivot->relationtype_id) {
                case config('constants.PRODUCT_INTERRELATION_ITEM') :
                case config('constants.PRODUCT_INTERRELATION_UPGRADE') :
                    OrderproductRepo::createHiddenOrderproduct(
                        $order_id,
                        $product->id,
                        $price,
                        $price,
                        instalmentQty: $orderProduct?->instalmentQty,
                        paidPercent: $orderProduct?->paidPercent
                    );
                    break;
                case config('constants.PRODUCT_INTERRELATION_GIFT') :
                    OrderproductRepo::createGiftOrderproduct($order_id, $product->id, $price);
                    break;
            }
        });
    }

}
