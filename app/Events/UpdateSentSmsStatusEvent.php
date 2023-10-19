<?php

namespace App\Events;

use App\Models\SMS;
use App\Models\SMS;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateSentSmsStatusEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public SMS $sms;

    /**
     * Create a new event instance.
     * UpdateSentSmsStatusEvent constructor.
     *
     * @param  SMS  $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
