<?php

namespace App\Http\Controllers\Api;

use App\Classes\VerificationCode;
use App\Events\MobileVerified;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResendToGuestRequest;
use App\Http\Requests\SubmitVerificationCode;
use App\Http\Requests\VerifyGuestRequest;
use App\Models\User;
use App\Notifications\VerifyGuestMobile;
use App\Repositories\NewsletterRepo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;

class MobileVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:1,1')->only('resend');
        $this->middleware('throttle:10,1')->only('verify');
    }

    /**
     * Mark the authenticated user's mobile number as verified.
     *
     * @param  SubmitVerificationCode  $request
     *
     * @return JsonResponse
     */
    public function verify(SubmitVerificationCode $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedMobile()) {
            return myAbort(Response::HTTP_FORBIDDEN, Lang::get('verification.Your mobile number is verified.'));
        }

        if ($request->get('code') == $user->getMobileVerificationCode() && $user->markMobileAsVerified()) {
            event(new MobileVerified($user));
            return response()->json(['message' => Lang::get('verification.Your mobile number is verified.')]);
        }

        return myAbort(Response::HTTP_BAD_REQUEST, Lang::get('verification.Your code is wrong.'));
    }

    /**
     * Resend the mobile verification notification.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function resend(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->hasVerifiedMobile()) {
            return myAbort(Response::HTTP_FORBIDDEN, Lang::get('verification.Your mobile number is verified.'));
        }

        $user->sendMobileVerificationNotification();

        if (isDevelopmentMode()) {
            return response()->json([
                'message' => Lang::get('verification.Verification code is sent.'),
                'code' => $user->getMobileVerificationCode(),
            ]);

        }
        return response()->json([
            'message' => Lang::get('verification.Verification code is sent.'),
        ]);
    }

    public function resendGuest(ResendToGuestRequest $request)
    {
        $action = VerificationCode::RESEND_GUST;
        $code = VerificationCode::getCode($action, $request->get('mobile'));
        Notification::route('mobile', $request->get('mobile'))->notify(new VerifyGuestMobile($code));

        if (isDevelopmentMode()) {
            return response()->json([
                'data' => ['expiration_time' => VerificationCode::TTLS[$action], 'code' => $code],
                'message' => 'کد تایید برای شما ارسال شد'
            ]);
        }
        return response()->json([
            'data' => ['expiration_time' => VerificationCode::TTLS[$action]], 'message' => 'کد تایید برای شما ارسال شد'
        ]);
    }

    public function verifyMoshavereh(VerifyGuestRequest $request)
    {
        if (NewsletterRepo::find($request->get('mobile'))) {
            return response()->json(['data' => ['is_active' => true, 'verified' => true], 'message' => 'کد تایید شد']);
        }

        auth()->user()?->update(['mobile_verified_at' => now()]);

        return response()->json(['data' => ['is_active' => false, 'verified' => false], 'message' => 'کد تایید شد']);
    }
}
