<?php

namespace App\Listeners\Plan;

class SyncContentsToStudyPlanListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $event->studyPlan->contents()->sync($event->contents);
    }
}
