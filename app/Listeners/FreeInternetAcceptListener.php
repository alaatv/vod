<?php

namespace App\Listeners;

use App\Events\FreeInternetAccept;

class FreeInternetAcceptListener
{
    /**
     * Handle the event.
     *
     * @param  FreeInternetAccept  $event
     *
     * @return void
     */
    public function handle(FreeInternetAccept $event)
    {
        $event->user->notify(new \App\Notifications\FreeInternetAccept());
    }
}
