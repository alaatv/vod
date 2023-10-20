<?php

namespace App\Events;

use App\Models\SMS;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogSendBulkSmsEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $sms;
    public $old_sms;
    public $user;

    /**
     * Create a new event instance.
     * LogSendBulkSmsEvent constructor.
     *
     * @param  array  $sms
     * @param  SMS|null  $oldSms
     * @param  User|null  $user
     */
    public function __construct(array $sms, SMS $oldSms = null, User $user = null)
    {
        $this->sms = $sms;
        if (isset($oldSms)) {
            $this->old_sms = $oldSms;
        }
        if (isset($user)) {
            $this->user = $user;
        }
    }
}
