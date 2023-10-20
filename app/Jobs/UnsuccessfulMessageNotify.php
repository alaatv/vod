<?php

namespace App\Jobs;

use App\Models\SMS;
use App\Models\SmsDetail;
use App\Models\SmsResult;
use App\Traits\APIRequestCommon;
use App\Traits\IppanelCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnsuccessfulMessageNotify implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use APIRequestCommon;
    use IppanelCommon;

    protected $sms;

    /**
     * Create a new job instance.
     *
     * @param  SMS  $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $smsDetail = $this->sms->detail;

        $penultimateUnsuccessfulSms = SmsDetail::query()->whereIn('sms_result_id',
            [SmsResult::SEND_FAIL_ID, SmsResult::RESPONSE_ERROR_ID])
            ->orderByDesc('created_at')
            ->offset(1)
            ->limit(1)
            ->first();

        if (
            !$penultimateUnsuccessfulSms ||
            diffInMinutes($penultimateUnsuccessfulSms->created_at,
                $smsDetail['created_at']) >= config('services.medianaSMS.UNSUCCESSFUL_MESSAGE_NOTIFICATION_INTERVAL')
        ) {
            // Notif to admin by email or admin panel ticket or ...
        }
    }
}
