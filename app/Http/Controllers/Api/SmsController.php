<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendBulkSmsRequest;
use App\Http\Requests\SmsPatternRequest;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Repositories\SMSRepository;
use App\Repositories\SmsUserRepository;
use App\Traits\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
}

