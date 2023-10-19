<?php

namespace App\Listeners;

use App\Events\GetLiveConductor;

class AttachLiveConductorToUser
{
    /**
     * Handle the event.
     *
     * @param  GetLiveConductor  $event
     * @return void
     */
    public function handle(GetLiveConductor $event): void
    {
        $event->liveConductor->users()->attach($event->userId);
    }
}
