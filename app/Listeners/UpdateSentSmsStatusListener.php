<?php

namespace App\Listeners;

use App\Events\UpdateSentSmsStatusEvent;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;

class UpdateSentSmsStatusListener
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
     * @param  UpdateSentSmsStatusEvent  $event
     */
    public function handle(UpdateSentSmsStatusEvent $event)
    {
        $sms = $event->sms;

        $this->updateSentMessageStatus($sms);
        $this->updateRecipientsStatus($sms);
    }
}
