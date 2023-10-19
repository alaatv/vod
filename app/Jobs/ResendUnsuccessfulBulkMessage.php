<?php

namespace App\Jobs;

use App\Models\SMS;
use App\Models\SMS;
use App\Models\SmsProvider;
use App\Models\SmsProvider;
use App\Models\User;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResendUnsuccessfulBulkMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use APIRequestCommon;
    use Helper;

    protected SMS $sms;
    protected User $user;

    /**
     * Create a new job instance.
     *
     * ResendUnsuccessfulBulkMessage constructor.
     * @param  SMS  $sms
     * @param  User|null  $user
     */
    public function __construct(SMS $sms, User $user = null)
    {
        $this->sms = $sms;
        if (isset($user)) {
            $this->user = $user;
        }
    }

    /**
     * Execute the job.
     *
     * @return JsonResponse
     */
    public function handle()
    {
        $sms = $this->sms;

        $smsUsers = $sms->users;
        $to = [];
        foreach ($smsUsers as $smsUser) {
            $to[] = [
                'mobile' => baseTelNo($smsUser->mobile),
                'id' => baseTelNo($smsUser->id),
            ];
        }

        if (!count($to)) {
            return;
        }
        $smsInfo = [
            'message' => $sms->message,
            'to' => $to,
            'from' => SmsProvider::find($sms->provider_id)->number,
        ];

        $response = $this->medianaSendSMS($smsInfo);
        LogSendBulkSms::dispatch($response, $this->user, $sms);

        if ($response['error']) {
            $msg = $response['has_response'] ? 'سامانه پیامکی پاسخ خطا برمیگرداند!' : 'شکست در استفاده از سامانه پیامکی!';
            return response()->json(['message' => $msg], Response::HTTP_SERVICE_UNAVAILABLE);
        }

    }
}
