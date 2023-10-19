<?php

namespace App\Listeners;

use App\Events\UnFavoredContent;

class MakeTimePointsAsUnFavored
{
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
     * @param  UnFavoredContent  $event
     * @return void
     */
    public function handle(UnFavoredContent $event)
    {
        foreach ($event->times as $timePoint) {
            $timePoint->unfavoring($event->user);
        }
    }
}
