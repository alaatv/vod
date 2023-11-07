<?php

namespace App\Http\Controllers\Api;

use App\Events\ResendUnsuccessfulBulkMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendBulkSmsRequest;
use App\Http\Requests\SmsPatternRequest;
use App\Models\SMS;
use App\Models\SmsResult;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Repositories\SMSRepository;
use App\Repositories\SmsUserRepository;
use App\Traits\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class SmsController extends Controller
{
    use Helper;

    public function __construct()
    {
        $authException = $this->getAuthExceptionArray();
        $this->callMiddlewares($authException);
    }

    /**
     *
     * @return array
     */
    private function getAuthExceptionArray(): array
    {
        return ['pattern', 'sendBulk'];
    }

    /**
     * @param  array  $authException
     */
    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
        $this->middleware('permission:'.config('constants.LOG_SMS_ADMIN_PANEL_ACCESS'), ['only' => 'index']);
        $this->middleware('permission:'.config('constants.SEND_SMS_TO_USER_ACCESS'), ['only' => 'sendBulk']);
        $this->middleware(['SMS_IP'], ['only' => ['pattern', 'pattern']]);
        $this->middleware('permission:'.config('constants.SEND_SMS_ADMIN_PANEL_ACCESS'),
            ['only' => ['getCreditForMediana']]);
    }

    public function index(Request $request)
    {
        $sms = SMSRepository::filter($request->all(), ['id'])
            ->toArray();

        $users = SmsUserRepository::filter(['sms' => $sms], ['user_id'], true)->pluck('user_id')->toArray();

        return response()->json($users);
    }

    public function pattern(SmsPatternRequest $request, User $user)
    {
        try {
            $user->notify(new GeneralNotification($request->get('pattern'),
                $request->get('params'),
                $request->get('reference_type'),
                $request->get('reference_id')));
            return response()->json(['message' => 'ok'], Response::HTTP_OK);
        } catch (Throwable $throwable) {
            return response()->json(['message' => 'fail'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function sendBulk(SendBulkSmsRequest $request)
    {
        $message = $request->get('message');
        $users = User::id($request->get('user_id'))->get();
        $mobiles = $users->pluck('mobile')->toArray();

        $from = $this->getProviderFromRequest($request->all());
        $response = $this->medianaSendBulkSMS($message, $mobiles, $from, users: $users);


        return $this->medianaHumanReadableResponse($response);
    }

    public function getCreditForMediana()
    {
        return (int) $this->medianaGetCredit();
    }

    public function resendUnsuccessfulBulkSms(Request $request, SMS $sms)
    {
        $smsResult = $sms->detail->sms_result_id;

        // Retry to send unsuccessful message.
        if (
            $smsResult != SmsResult::DONE_ID &&
            !isset($sms->detail->pattern_code) &&
            is_null($sms->detail->resent_sms_id)
        ) {
            // TODO: Currently the resending operation is for notification SMS only. We need to modify it so that it can be used to resend bulk sms as correct.
            event(new ResendUnsuccessfulBulkMessageEvent($sms));
            return response()->json(['message' => 'پیامک دسته ای در صف ارسال مجدد قرار گرفت.']);
        }
        return response()->json(['error' => 'عدم ارسال مجدد پیامک. فقط پیامک های دسته ای شکست خورده قابلیت ارسال مجدد دارند!'],
            400);
    }

    public function sendSMS(Request $request)
    {
        $from = $this->getProviderFromRequest($request->all());
        $message = $request->get('message');
        $usersId = $request->get('users');

        $mobiles = User::whereIn('id', explode(',', $usersId))
            ->pluck('mobile')
            ->toArray();

        if (empty($mobiles)) {
            return response()->json([], ResponseAlias::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
        }

        $response = $this->medianaSendBulkSMS($message, $mobiles, $from);

        if ($response['error']) {
            $msg = $response['has_response'] ? 'سامانه پیامکی پاسخ خطا برمیگرداند!' : 'شکست در استفاده از سامانه پیامکی!';
            return response()->json(['message' => $msg], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
        }

        $smsCredit = $this->medianaGetCredit();
        return response()->json($smsCredit);
    }
}

