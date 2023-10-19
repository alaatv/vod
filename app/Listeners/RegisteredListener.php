<?php

namespace App\Listeners;

class RegisteredListener
{
    public function handle($event)
    {
        setcookie('nocache', '1', time() + (86400 * 30), '/');
    }
}
