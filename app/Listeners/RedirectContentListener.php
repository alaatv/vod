<?php

namespace App\Listeners;

use App\Events\ContentRedirected;
use App\Jobs\RemoveContentPamphletFiles;
use App\Jobs\RemoveContentVideoFiles;

class RedirectContentListener
{
    /**
     * Handle the event.
     *
     * @param  ContentRedirected  $event
     *
     * @return void
     */
    public function handle(ContentRedirected $event)
    {
        if ($event->content->contenttype_id == config('constants.CONTENT_TYPE_VIDEO')) {
            dispatch(new RemoveContentVideoFiles($event->content));
        } else {
            if ($event->content->contenttype_id == config('constants.CONTENT_TYPE_PAMPHLET')) {
                dispatch(new RemoveContentPamphletFiles($event->content));
            }
        }
    }
}
