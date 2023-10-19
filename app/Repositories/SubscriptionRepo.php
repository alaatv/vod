<?php

namespace App\Repositories;

use App\Classes\CacheFlush;
use App\Classes\CouponSubmitter;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubscriptionRepo
{
    public static function createSubscription(int $userId, int $eventId): ?Subscription
    {
        return Subscription::create([
            'user_id' => $userId,
            'event_id' => $eventId,
        ]);
    }

    public static function validProductSubscriptionOfUser(int $userId, array $productsId): ?Subscription
    {
        $result = Cache::tags(['user_'.$userId])->remember('user:subscription:'.'U'.$userId.'-P'.implode(',',
                $productsId), config('constants.CACHE_60'), function () use ($userId, $productsId) {
            return Subscription::query()
                ->where('user_id', $userId)
                ->where('subscription_type', Product::class)
                ->whereIn('subscription_id', $productsId)
                ->notExpired()
                ->first() ?? false;
        });
        if (isset($result) && $result != false && !($result instanceof Subscription)) {
            Log::error(json_encode([
                'result' => $result, 'user_id' => $userId, '$productsId' => implode(',', $productsId)
            ], JSON_UNESCAPED_UNICODE));
            return null;
        }
        if ($result === false) {
            return null;
        }
        return $result;
    }

    public static function userSubscriptions(int $userId, int $seller)
    {
        return Subscription::query()
            ->where('user_id', $userId)
            ->where('seller', $seller)
            ->with('subscription')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getValue(Subscription $subscription, $access)
    {
        return collect($subscription->values)->where('title', $access)->firstOrFail();
    }

    public static function incrementValue(Subscription $subscription, $access, $increment)
    {
        $updatedValues = collect($subscription->values)->each(function ($value) use ($access, $increment) {
            if ($value->title == $access && $value->usageLimit != config('constants.ATTRIBUTE_VALUE_INFINITE')) {
                $value->usage += $increment;
            }
            return $value;
        });
        $subscription->update(['values' => $updatedValues->toArray()]);
    }


    public static function createYaldaSubscription(?User $user)
    {
        if ($user === null) {
            return null;
        }

        $discount = CouponRepo::getRandomDiscount();
        $maximumNumberOfUsage = $discount != 100 ? 2 : 1;

        $coupon = CouponRepo::makeRandomPartialCoupon(
            'c4y',
            $discount,
            'چله پر جایزه',
            $maximumNumberOfUsage,
            null,
            now('Asia/Tehran'),
            '2022-03-21 00:00:00',
        );

        $order = $user->getOpenOrderOrCreate();
        $coupon->products()->sync(Product::ALL_SINGLE_ABRISHAM_PRODUCTS);
        $coupon->users()->attach([$user->id]);


        //Todo : fix this
        CacheFlush::flushYaldaTag($user->id);
        Cache::tags(['coupon_user_'.$user->id])->flush();
        do {
            $coupon = $user->getMaximumActiveCoupon();
        } while (!isset($coupon));


        (new CouponSubmitter($order))->submit($coupon);


        return $coupon;
    }
}
