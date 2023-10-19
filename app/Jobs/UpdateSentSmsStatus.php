<?php

namespace App\Jobs;

use App\Events\UpdateSentSmsStatusEvent;
use App\Models\SMS;
use App\Models\SMS;
use App\Traits\IppanelCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSentSmsStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IppanelCommon;

    public $queue;
    protected SMS $sms;

    /**
     * UpdateSentSmsStatus constructor.
     *
     * Create a new job instance.
     *
     * @param  SMS  $sms
     */
    public function __construct(SMS $sms)
    {
        $this->queue = 'default2';
        $this->sms = $sms;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $sms = $this->sms;

        event(new UpdateSentSmsStatusEvent($sms));
    }
}
