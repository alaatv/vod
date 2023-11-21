<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionInquiryRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Event;
use App\Repositories\SubscriptionRepo;
use App\Services\SubscriptionService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptoinController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'مهلت ثبت نام تمام شده است');

        $user = $request->user();
        try {
            $subscription = SubscriptionRepo::createSubscription($user->id, Event::ARASH_PUBLISH_EVENT_ID);
        } catch (QueryException $e) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'خطا در ثبت خبرنامه',
                ['user_id' => 'کاربر قبلا در خبرنامه ثبت نام کرده است']);
        }

        if (isset($subscription)) {
            return response()->json([
                'message' => 'User subscribed',
            ]);
        }

        return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'خطا در ثبت خبرنامه');
    }

    public function userSubscriptions(Request $request)
    {
        $user = auth('api')->user();
        $seller = $request->seller ?? config('constants.ALAA_SELLER');
        $subscriptions = SubscriptionRepo::userSubscriptions($user->id, $seller);
        return SubscriptionResource::collection($subscriptions);
    }

    public function subscriptionInquiry(SubscriptionInquiryRequest $request)
    {
        $seller = $request->seller ?? config('constants.ALAA_SELLER');
        return SubscriptionService::inquiry($request->input('access'), $seller, increment: $request->get('increment',
            1));
    }

    public function updateValue(SubscriptionInquiryRequest $request)
    {
        $seller = $request->seller ?? config('constants.ALAA_SELLER');
        return SubscriptionService::inquiry($request->input('access'), $seller, true, $request->get('increment', 1));
    }
}
