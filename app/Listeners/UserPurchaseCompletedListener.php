<?php

namespace App\Listeners;

use App\Classes\CacheFlush;
use App\Events\UserPurchaseCompleted;
use Illuminate\Support\Facades\Cache;

class UserPurchaseCompletedListener
{
    /**
     * Handle the event.
     *
     * @param  UserPurchaseCompleted  $event
     * insert content to content_income
     *
     * @return void
     */
    public function handle(UserPurchaseCompleted $event)
    {
        CacheFlush::flushAssetCache(optional($event->order)->user);
        Cache::tags('order_'.optional($event->order)->id)->flush();
        Cache::tags('coupon_user_'.optional($event->order)->user->id)->flush();
        CacheFlush::flushYaldaTag(optional($event->order)->user->id);
//        ContentInComeJob::dispatch($event->order);
    }
}
