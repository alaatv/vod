<?php

namespace App\Listeners;

use App\Events\UserAvatarUploaded;
use App\Jobs\RemoveOldProfilePhoto;
use Illuminate\Queue\InteractsWithQueue;

class RemoveOldUserAvatarListener
{
    use InteractsWithQueue;


    /**
     * Handle the event.
     *
     * @param  UserAvatarUploaded  $event
     *
     * @return void
     */
    public function handle(UserAvatarUploaded $event)
    {
        dispatch(new RemoveOldProfilePhoto(basename($event->user->getRawOriginal('photo')), $event->user));
    }
}
