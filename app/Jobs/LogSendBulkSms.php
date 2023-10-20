<?php

namespace App\Jobs;

use App\Models\SMS;
use App\Models\SmsResult;
use App\Models\User;
use App\Traits\IppanelCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class LogSendBulkSms implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IppanelCommon;

    public $queue;
    protected array $sms;
    protected User|null $user;
    protected SMS|null $old_sms;

    /**
     * LogSendBulkSms constructor.
     *
     * Create a new job instance.
     *
     * @param  array  $sms
     * @param  User|null  $user
     * @param  SMS|null  $oldSms
     */
    public function __construct(array $sms, User $user = null, SMS $oldSms = null)
    {
        $this->queue = 'default2';
        $this->sms = $sms;
        $this->user = $user;
        $this->old_sms = $oldSms;
    }

    /**
     * Execute the job.
     *
     * @return false
     */
    public function handle()
    {
        if (isset($this->user)) {
            $user = $this->user;
        }
        if (isset($this->old_sms)) {
            $oldSms = $this->old_sms;
        }

        $info = $this->sms;

        $result = Arr::get($info, 'result');
        $error = Arr::get($info, 'error');
        $hasResponse = Arr::get($info, 'has_response');
        $bulkId = $error ? null : Arr::get($info, 'response');
        $message = Arr::get($info, 'message');

        $to = Arr::get($result, 'to');
        $smsProviderId = Arr::get($result, 'provider_id');

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
