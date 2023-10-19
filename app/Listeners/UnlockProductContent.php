<?php

namespace App\Listeners;

use App\Events\UserPurchaseCompleted;

class UnlockProductContent
{
    /**
     * Handle the event.
     *
     * @param  UserPurchaseCompleted  $event
     * @return void
     */
    public function handle(UserPurchaseCompleted $event)
    {
        if ($event->order->isInInstalment) {
            $installment_no = $event->order->transactions()->successful()->count();
            $event->order->orderproducts->each(function ($orderProduct) use ($installment_no) {
                if ($installment_no <= count($orderProduct->instalmentQty)) {
                    $orderProduct->paidPercent = array_sum(array_slice($orderProduct->instalmentQty, 0,
                        $installment_no));
                    $orderProduct->save();
                }
            });
        }
    }
}
