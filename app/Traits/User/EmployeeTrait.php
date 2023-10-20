<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 16:12
 */

namespace App\Traits\User;

use App\Collection\UserCollection;
use App\Models\Employeeschedule;
use App\Models\Employeetimesheet;
use Cache;


trait EmployeeTrait
{
    /**
     * @return UserCollection
     */
    public static function getEmployee(): UserCollection
    {
        $key = 'getEmployee';

        return Cache::tags(['employee'])
            ->remember($key, config('constants.CACHE_600'), function () {
                $employees = User::select()
                    ->role([config('constants.ROLE_EMPLOYEE_ID')])
                    ->orderBy('lastName')
                    ->get();

                return $employees;
            });
    }

    public function employeeschedules()
    {
        return $this->hasMany(Employeeschedule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | static methods
    |--------------------------------------------------------------------------
    */

    public function employeetimesheets()
    {
        return $this->hasMany(Employeetimesheet::class);
    }
}
