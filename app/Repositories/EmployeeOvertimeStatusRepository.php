<?php

namespace App\Repositories;

use App\Models\Employeeovertimestatus;
use App\Models\Employeeovertimestatus;

class EmployeeOvertimeStatusRepository extends AlaaRepo
{
    public static function getModelClass(): string
    {
        return Employeeovertimestatus::class;
    }
}
