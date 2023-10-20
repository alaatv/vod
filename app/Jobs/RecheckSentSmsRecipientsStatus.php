<?php

namespace App\Jobs;

use App\Models\SMS;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecheckSentSmsRecipientsStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use APIRequestCommon;
    use Helper;

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
        $sms = $this->sms;
        $response = $this->updateRecipientsStatus($sms);

        if ($this->isAllowedRecheckSmsStatus($response, $sms)) {
            RecheckSentSmsRecipientsStatus::dispatch($sms)->delay(recheckSentSmsStatusTime());
        }

        // TODO: Notif to admin. باید در مورد نحوه نوتیف دادن به ادمین فکر شود.
    }
}
