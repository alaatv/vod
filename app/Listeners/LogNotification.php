<?php

namespace App\Listeners;

use App\Events\ResendUnsuccessfulMessageEvent;
use App\Events\UnsuccessfulMessageNotifyEvent;
use App\Models\SmsResult;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Arr;

class LogNotification
{
    use APIRequestCommon;
    use Helper;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotificationSent  $event
     * @return bool
     */
    public function handle(NotificationSent $event)
    {
        $info = $event->response;

        $result = Arr::get($info, 'result');
        if (!isset($result)) {
            return 0;
        }

        $error = Arr::get($info, 'error');
        $hasResponse = Arr::get($info, 'has_response');
        $bulkId = $error ? null : Arr::get($info, 'response');
        $message = Arr::get($info, 'message');

        $to = Arr::get($result, 'to');
        $smsProviderId = Arr::get($result, 'provider_id');
        $patternCode = Arr::get($result, 'pattern_code');
        $patternData = Arr::get($result, 'pattern_data');
        $foreign_id = Arr::get($result['log_data'], 'reference_id');
        $foreign_type = Arr::get($result['log_data'], 'reference_type');

        $smsResult = !$error ? SmsResult::DONE_ID : ($hasResponse ? SmsResult::RESPONSE_ERROR_ID : SmsResult::SEND_FAIL_ID);

        $sms = $this->logSentSms(
            $to,
            $smsProviderId,
            $message,
            $patternCode,
            $patternData,
            $bulkId,
            $smsResult,
            foreign_id: $foreign_id,
            foreign_type: $foreign_type);

        // Retry to send unsuccessful message.
        if ($smsResult == SmsResult::SEND_FAIL_ID) {
            event(new ResendUnsuccessfulMessageEvent($sms));
        }

        // Notify to admin if message response has error.
        if (in_array($smsResult, [SmsResult::SEND_FAIL_ID, SmsResult::RESPONSE_ERROR_ID])) {
            event(new UnsuccessfulMessageNotifyEvent($sms));
        }
    }
}
