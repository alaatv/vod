<?php


namespace App\Classes;


use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CacheFlush
{
    public const YALDA_1400_TAG = 'yalda_discount_for_user_';

    public static function flushAssetCache(?User $user)
    {
        if (is_null($user)) {
            return null;
        }

        Cache::tags([
            'user_'.$user->id.'_closedOrders',
            'user_'.$user->id.'_transactions',
            'user_'.$user->id.'_instalments',
            'user_'.$user->id.'_totalBonNumber',
            'user_'.$user->id.'_validBons',
            'user_'.$user->id.'_hasBon',
            'user_'.$user->id.'_obtainPrice',
            'userAsset_'.$user->id,
            CacheFlush::YALDA_1400_TAG.$user->id
        ])->flush();
    }

    public static function flushYaldaTag(?int $userId)
    {
        Cache::tags([CacheFlush::YALDA_1400_TAG.$userId])->flush();
        Cache::tags(['coupon_user_'.$userId])->flush();
        Cache::tags(['getShowDiscountAttribute:'.$userId])->flush();
//        Cache::store('octane')->forgetByPrefix('yc:'.$userId);
    }
}
