<?php

namespace App\Events;

use App\Models\SMS;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResendUnsuccessfulMessageEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $sms;

    /**
     * Create a new event instance.
     * ResendUnsuccessfulMessageEvent constructor.
     *
     * @param  SMS  $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
