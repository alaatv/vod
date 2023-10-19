<?php

namespace App\Observers;

use App\Models\Event;
use Cache;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     *
     * @param  Event  $event
     * @return void
     */
    public function created(Event $event)
    {
        //
    }

    /**
     * Handle the Event "updated" event.
     *
     * @param  Event  $event
     * @return void
     */
    public function updated(Event $event)
    {
        $tag = Event::getBindingCacheTagArray($event->id);
        Cache::tags($tag)->flush();
    }

    /**
     * Handle the Event "deleted" event.
     *
     * @param  Event  $event
     * @return void
     */
    public function deleted(Event $event)
    {
        //
    }

    /**
     * Handle the Event "restored" event.
     *
     * @param  Event  $event
     * @return void
     */
    public function restored(Event $event)
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     *
     * @param  Event  $event
     * @return void
     */
    public function forceDeleted(Event $event)
    {
        //
    }
}
