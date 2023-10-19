<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepo;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionService
{
    public static function inquiry($access, $seller, $updateValue = false, $increment = 1)
    {
        $user = auth('api')->user();
        $subscription = SubscriptionRepo::userSubscriptions($user->id, $seller)->where('valid_until', '>=',
            now())->first();
        if (!$subscription) {
            return myAbort(Response::HTTP_PAYMENT_REQUIRED, 'اشتراکی ندارید');
        }

        if (SubscriptionService::hasAccess($subscription, $access, $increment)) {
            if ($updateValue) {
                SubscriptionRepo::incrementValue($subscription, $access, $increment);
            }
            return response()->json(['message' => 'کاربر مجوز لازم را دارد']);
        }

        return response()->json(['message' => 'کاربر مجوز لازم را ندارد'], JsonResponse::HTTP_FORBIDDEN);
    }

    public static function hasAccess(Subscription $subscription, $access, $increment)
    {
        $access = SubscriptionRepo::getValue($subscription, $access);
        if (($access->usageLimit == config('constants.ATTRIBUTE_VALUE_INFINITE')) || $access->usage + $increment <= $access->usageLimit) {
            return true;
        }
        return false;
    }

}
