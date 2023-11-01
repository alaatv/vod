<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SubscriptionRepo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SubscriptionController extends Controller
{

    public function __construct()
    {
        $this->middleware('yaldaSubscription')->only(['getYaldaDiscount']);
    }

    public function getYaldaDiscount(Request $request)
    {
        $coupon = SubscriptionRepo::createYaldaSubscription($request->user());

        if (!isset($coupon)) {
            return myAbort(ResponseAlias::HTTP_BAD_REQUEST, trans('yalda1400.no discount is available now'));
        }
//        return response()->json(['message' => 'دریافت تخفیف یلدای ۱۴۰۰ غیر فعال شده است'], Response::HTTP_LOCKED);
        return response()->json([
            'data' => [
                'discount' => $coupon?->discount,
            ]
        ]);
    }
}