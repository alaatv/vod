<?php

namespace App\Listeners\Plan;

use Illuminate\Support\Facades\Cache;

class SyncContentsToPlanListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $event->plan->contents()->sync($event->contents);

        Cache::tags(['event_abrisham1401_whereIsKarvan_'.optional($event->plan->studyplan)->plan_date])->flush();
    }
}
