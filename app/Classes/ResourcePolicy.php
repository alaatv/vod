<?php


namespace App\Classes;


use App\Models\User;
use Exception;

class ResourcePolicy
{
    public static function check(User $authUser, User $owner)
    {
        if ($authUser->id != $owner->id) {
            throw new Exception('This Action Forbidden For Auth User', '403');
        }

    }

}
