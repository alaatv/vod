<?php

namespace App\Listeners;

use App\Events\ResendUnsuccessfulMessageEvent;
use App\Jobs\ResendUnsuccessfulMessage;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;

class ResendUnsuccessfulMessageListener
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
     * @param  ResendUnsuccessfulMessageEvent  $event
     * @return void
     */
    public function handle(ResendUnsuccessfulMessageEvent $event)
    {
        $sms = $event->sms;
        ResendUnsuccessfulMessage::dispatch($sms)->delay(resendUnsuccessfulMessageTime());
    }
}
