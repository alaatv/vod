<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class EmployeeScheduleRepository
{
    /**
     * @return Builder
     */
    public static function doesntHaveSchedulesEmployees(): Builder
    {
        return User::whereHas('roles', function ($q) {
            $q->where('name', config('constants.ROLE_EMPLOYEE'));
        })
            ->whereDoesntHave('employeeschedules');
    }
}
