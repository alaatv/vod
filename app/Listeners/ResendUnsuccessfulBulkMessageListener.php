<?php

namespace App\Listeners;

use App\Events\ResendUnsuccessfulBulkMessageEvent;
use App\Jobs\ResendUnsuccessfulBulkMessage;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;

class ResendUnsuccessfulBulkMessageListener
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
     * @param  ResendUnsuccessfulBulkMessageEvent  $event
     * @return void
     */
    public function handle(ResendUnsuccessfulBulkMessageEvent $event)
    {
        $sms = $event->sms;
        ResendUnsuccessfulBulkMessage::dispatch($sms, auth()->user());
    }
}
