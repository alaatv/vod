<?php

namespace App\Repositories;


use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class GeneralRepo
{

    /**
     * shomaresh sefaresh haye fizik abrisham
     *
     * @return Order|Builder
     */
    public static function fizikAbrishamCount()
    {
        $order =
            Order::query()->paidAndClosed()->where('orderstatus_id', '<>',
                config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'))
                ->where('completed_at', '<=', '2022-07-06 00:00:00')
                ->where('completed_at', '>', '2021-06-22 00:00:00')
                ->whereHas('orderproducts', function ($q) {
                    $q->whereIn('product_id', [Product::RAHE_ABRISHAM99_PACK_TAJROBI]);
//            $q->whereIn('product_id' , [Product::RAHE_ABRISHAM99_PACK_RIYAZI ]) ;
                })->whereHas('orderproducts', function ($q) {
                    $q->where('orderproducttype_id', '<>', config('constants.ORDER_PRODUCT_GIFT'))
                        ->whereIn('product_id', [Product::RAHE_ABRISHAM99_FIZIK_RIYAZI]); //toloie
//                ->whereIn('product_id' , [Product::RAHE_ABRISHAM99_FIZIK_TAJROBI ]) ; //kazer
                })
                ->whereHas('transactions', function ($q3) {
                    $q3->where('transactionstatus_id', 3)->where('paymentmethod_id', '<>',
                        config('constants.PAYMENT_METHOD_WALLET'));
                });

        return $order;
    }
}
