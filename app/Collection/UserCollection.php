<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-03
 * Time: 19:06
 */

namespace App\Collection;

use Illuminate\Database\Eloquent\Collection;

class UserCollection extends Collection
{
    public function roleFilter(array $rolesId): UserCollection
    {
        $users = $this->whereHas('roles', function ($q) use ($rolesId) {
            $q->whereIn('id', $rolesId);
        });

        return $users;
    }

    public function majorFilter($majorsId): UserCollection
    {
        if (in_array(0, $majorsId)) {
            $users = $this->whereDoesntHave('major');
        } else {
            $users = $this->whereIn('major_id', $majorsId);
        }

        return $users;
    }

    /**
     * Makes uniques collection of this user collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUniqueUsers()
    {
        $uniqueUsers = $this->groupBy('nationalCode');
        $users = collect();
        foreach ($uniqueUsers as $user) {
            $verifiedUsers = $user->getUsersWithVerifiedMobiles();
            if ($verifiedUsers->isNotEmpty()) {
                $users->push($verifiedUsers->first());
            } else {
                $users->push($user->first());
            }
        }

        return $users;
    }

    public function getUsersWithVerifiedMobiles()
    {
        return $this->where('mobile_verified_at', '<>', null);
    }
}
