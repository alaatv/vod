<?php

namespace App\Observers;

use App\Models\Order;
use App\Repositories\Loging\ActivityLogRepo;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        if ((auth()->id() != $order->user->id)) {
            ActivityLogRepo::LogAddOrder(auth()->user(), $order);
        }
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }

    public function saved(Order $order)
    {
        Cache::tags(['order_'.$order->id, 'user_'.$order->user->id.'_orders'])->flush();
    }
}
