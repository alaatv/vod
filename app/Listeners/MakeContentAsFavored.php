<?php

namespace App\Listeners;

use App\Events\FavoredTimePoint;

class MakeContentAsFavored
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
     * @param  FavoredTimePoint  $event
     * @return void
     */
    public function handle(FavoredTimePoint $event)
    {
        $event->content->favoriteBy()->syncWithoutDetaching($event->user);
    }
}
