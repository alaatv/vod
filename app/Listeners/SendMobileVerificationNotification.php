<?php

namespace App\Listeners;

use App\Classes\Verification\MustVerifyMobileNumber;
use Illuminate\Auth\Events\Registered;

class SendMobileVerificationNotification
{
    /**
     * Handle the event.
     *
     * @param  Registered  $event
     *
     * @return void
     */
    public function handle(Registered $event)
    {
        if ($event->user instanceof MustVerifyMobileNumber && !$event->user->hasVerifiedMobile()) {
            $event->user->sendMobileVerificationNotification();
        }
    }
}
