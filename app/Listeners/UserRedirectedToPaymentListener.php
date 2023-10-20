<?php

namespace App\Listeners;

use App\Classes\CacheFlush;
use App\Events\UserRedirectedToPayment;
use Illuminate\Support\Facades\Cache;

class UserRedirectedToPaymentListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRedirectedToPayment  $event
     *
     * @return void
     */
    public function handle(UserRedirectedToPayment $event)
    {
        CacheFlush::flushAssetCache(optional($event->order)->id);
        CacheFlush::flushYaldaTag(optional($event->user)->id);
        Cache::tags('order_'.optional($event->order)->id)->flush();
        Cache::tags('transaction_'.optional($event->transaction)->id)->flush();
        Cache::tags('coupon_user_'.optional($event->user)->id)->flush();
    }
}
