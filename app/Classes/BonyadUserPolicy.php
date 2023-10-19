<?php


namespace App\Classes;


use App\Models\User;
use App\Repositories\UserRepo;
use App\Services\BonyadService;
use Illuminate\Validation\UnauthorizedException;

class BonyadUserPolicy
{
    public static function check(User $user)
    {
        $authUser = auth('api')->user();
        $roles = BonyadService::getRoles();
        $rolesKey = collect(array_keys($roles));
        $authUserRoles = $authUser->roles()->pluck('name');
        $userRole = $rolesKey->intersect($authUserRoles)->first();
        if (is_null($userRole) or !UserRepo::userAccess($authUser->id, $user->id, $roles[$userRole])) {
            throw new UnauthorizedException('This Action Forbidden For This User', 403);
        }
    }

}
