<?php


namespace App\Classes;


use App\Models\User;
use App\Services\BonyadService;
use Illuminate\Validation\UnauthorizedException;


class LiveDescriptionPolicy
{
    public static function check(User $authUser, int $owner)
    {
        $bonyad = $authUser->roles()->pluck('name')->intersect(array_keys(BonyadService::getRoles()));
        if ($bonyad->isNotEmpty()) {
            $realOwner = config('constants.BONYAD_OWNER');
        } else {
            $realOwner = config('constants.ALAA_OWNER');
        }

        if ($realOwner != $owner) {
            throw new UnauthorizedException('This Action Forbidden For This Owner', '403');
        }
    }

}
