<?php

namespace App\Listeners;

use App\Events\LogSendBulkSmsEvent;
use App\Models\SmsResult;
use App\Models\SmsResult;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;

class LogSendBulkSmsListener
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
     * @param  LogSendBulkSmsEvent  $event
     * @return bool
     */
    public function handle(LogSendBulkSmsEvent $event)
    {
        if (isset($event->old_sms)) {
            $oldSms = $event->old_sms;
        }
        if (isset($event->user)) {
            $user = $event->user;
        }

        $info = $event->sms;

        $result = $info['result'];
        $error = $info['error'];
        $hasResponse = $info['has_response'];
        $bulkId = $error ? null : $info['response'];
        $message = $info['message'];

        $to = $result['to'];
        $smsProviderId = $result['provider_id'];

        $smsResult = !$error ? SmsResult::DONE_ID : ($hasResponse ? SmsResult::RESPONSE_ERROR_ID : SmsResult::SEND_FAIL_ID);

        $sms = $this->logSentSms($to, $smsProviderId, $message, null, null, $bulkId, $smsResult, $user ?? null);

        if (isset($oldSms)) {
            $oldSms->detail->update(['resent_sms_id' => $sms->id]);
        }

        if (in_array($smsResult, [SmsResult::SEND_FAIL_ID, SmsResult::RESPONSE_ERROR_ID])) {
            return false;
        }
    }
}
