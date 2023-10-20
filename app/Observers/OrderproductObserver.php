<?php

namespace App\Observers;

use App\Models\Orderproduct;
use App\Services\OrderProductsService;
use Illuminate\Support\Facades\Cache;

class OrderproductObserver
{

    /**
     * Handle the orderproduct "created" event.
     *
     * @param  Orderproduct  $orderproduct
     *
     * @return void
     */
    public function created(Orderproduct $orderproduct)
    {
    }

    /**
     * Handle the orderproduct "updated" event.
     *
     * @param  Orderproduct  $orderproduct
     *
     * @return void
     */
    public function updated(Orderproduct $orderproduct)
    {
        //
    }

    /**
     * Handle the orderproduct "deleted" event.
     *
     * @param  Orderproduct  $orderproduct
     *
     * @return void
     */
    public function deleted(Orderproduct $orderproduct)
    {
        $orderProductsService = resolve(OrderProductsService::class);
        $product = $orderproduct->product;
        if ($product->complimentaryproducts->isNotEmpty()) {
            $order = $orderproduct->order;
            foreach ($product->complimentaryproducts as $complimentaryproduct) {
                if ($complimentaryproduct->pivot->is_dependent && $order->orderproducts->contains('product_id',
                        $complimentaryproduct->id)) {
                    $complimentaryOrderProduct = $order->orderproducts->where('product_id',
                        $complimentaryproduct->id)->first();
                    $orderProductsService->destroyOrderProduct($complimentaryOrderProduct);
                }
            }
        }
    }

    /**
     * Handle the orderproduct "restored" event.
     *
     * @param  Orderproduct  $orderproduct
     *
     * @return void
     */
    public function restored(Orderproduct $orderproduct)
    {
        //
    }

    /**
     * Handle the orderproduct "force deleted" event.
     *
     * @param  Orderproduct  $orderproduct
     *
     * @return void
     */
    public function forceDeleted(Orderproduct $orderproduct)
    {
        //
    }

    public function saving(Orderproduct $orderproduct)
    {


    }

    public function saved(Orderproduct $orderproduct)
    {
        Cache::tags([
            'order_'.$orderproduct->order_id, 'orderproduct_'.$orderproduct->id,
        ])->flush();
    }

}
