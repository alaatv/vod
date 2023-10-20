<?php

namespace App\Listeners;

use App\Events\ChangeDanaStatus;
use App\Services\DanaProductService;

class ChangeDanaStatusListener
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
     * @param  ChangeDanaStatus  $event
     * @return void
     */
    public function handle(ChangeDanaStatus $event)
    {
        DanaProductService::changeCourseStatus($event->courseId);
        DanaProductService::approveCourse($event->courseId);

    }
}
