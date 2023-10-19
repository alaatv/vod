<?php

namespace App\Listeners;

use App\Events\UnsuccessfulMessageNotifyEvent;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;

class UnsuccessfulMessageNotifyListener
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
     * @param  UnsuccessfulMessageNotifyEvent  $event
     */
    public function handle(UnsuccessfulMessageNotifyEvent $event)
    {
        $sms = $event->sms;
//        UnsuccessfulMessageNotify::dispatch($sms);
    }
}
