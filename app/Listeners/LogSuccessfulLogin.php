<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     *
     * @return void
     */
    public function handle(Login $event)
    {
        $userFullName = '';
        if (isset($event->user->gender->id)) {
            if (strcmp($event->user->gender->name, 'آقا') == 0) {
                $userFullName .= 'آقای ';
            } else {
                if (strcmp($event->user->gender->name, 'خانم') == 0) {
                    $userFullName .= 'خانم ';
                }
            }
        }
        if (isset($event->user->firstName)) {
            $userFullName .= $event->user->firstName;
        }
        if (isset($event->user->lastName)) {
            $userFullName .= ' '.$event->user->lastName;
        }

        //        if(strlen($userFullName) == 0 ) $userFullName = "کاربر ناشناس" ;
        session()->put('welcomeMessage', $userFullName.' '.'به آلاء خوش آمدی');
        //        session()->flash('welcome', $event->user->firstName .' '. 'به آلاء خوش آمدی');
        //changed by mohammad from the second line to the first line
    }
}
