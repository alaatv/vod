<?php

namespace App\Jobs;

use App\Events\UnsuccessfulMessageNotifyEvent;
use App\Models\SMS;
use App\Models\SMS;
use App\Models\SmsProvider;
use App\Models\SmsProvider;
use App\Models\SmsResult;
use App\Models\SmsResult;
use App\Traits\APIRequestCommon;
use App\Traits\IppanelCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class ResendUnsuccessfulMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use APIRequestCommon;
    use IppanelCommon;

    protected $sms;
    protected $logSMS;

//    public $tries = 10;

    /**
     * Create a new job instance.
     *
     * @param  SMS  $sms
     * @param  array  $options
     * @param  string  $queue
     */
    public function __construct(SMS $sms, array $options = [], string $queue = 'default')
    {
        $this->logSMS = Arr::get($options, 'logging', true);
        $this->sms = $sms;
        $this->queue = $queue;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $sms = $this->sms;

        $to = $sms->users[0]->mobile;
        $smsProviderId = $sms->provider_id;
        $patternCode = $sms->detail->pattern_code;
        $patternData = (array) json_decode($sms->detail->pattern_data);

        $info = $this->sendPatternSmsByApi($to, SmsProvider::find($smsProviderId)->number, $patternCode, $patternData);


//      $info        = $event->response;

//      $result      = $info['result'];
        $error = $info['error'];
        $hasResponse = $info['has_response'];
        $bulkId = $error ? null : $info['response'];
        $message = $info['message'];

//      $to          = $result['to'];
//      $from        = $result['from'];
//      $patternCode = $result['pattern_code'];
//      $patternData = $result['pattern_data'];

        $smsResult = !$error ? SmsResult::DONE_ID : ($hasResponse ? SmsResult::RESPONSE_ERROR_ID : SmsResult::SEND_FAIL_ID);

        if ($this->logSMS) {
            $sms = $this->logSentSms([$to], $smsProviderId, $message, $patternCode, $patternData, $bulkId, $smsResult);
        }

        // TODO: The release() method inside the job does the same as the delay() method outside the job. It is used
        //  with the $tries property together. Please see https://divinglaravel.com/laravel-queues-in-action-running-the-same-job-multiple-times
//        $this->release(resendUnsuccessfulMessageTime());

        // Retry to send unsuccessful message.
        if ($smsResult == SmsResult::SEND_FAIL_ID) {
//            event(new ResendUnsuccessfulMessageEvent($sms));
        }
        // Notify to admin if message response has error.
        if (in_array($smsResult, [SmsResult::SEND_FAIL_ID, SmsResult::RESPONSE_ERROR_ID])) {
            event(new UnsuccessfulMessageNotifyEvent($sms));
        }
    }
}
