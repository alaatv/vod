<?php

namespace App\Listeners;

use App\Events\Authenticated;

class AuthenticatedListener
{
    /**
     * Handle the event.
     *
     * @param  Authenticated  $event
     *
     * @return void
     */
    public function handle(Authenticated $event)
    {
        setcookie('nocache', '1', time() + (86400 * 30), '/');
    }
}
