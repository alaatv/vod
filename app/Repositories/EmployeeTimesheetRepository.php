<?php

namespace App\Repositories;

use App\Models\Employeetimesheet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class EmployeeTimesheetRepository extends AlaaRepo
{
    public static function getModelClass(): string
    {
        return Employeetimesheet::class;
    }

    /**
     * @param  array|null  $userIds
     * @param  string|null  $fromDate
     * @param  string|null  $toDate
     * @return Employeetimesheet|Builder
     */
    public static function getEmployeeTimeSheets(
        ?array $userIds = null,
        ?string $fromDate = null,
        ?string $toDate = null
    ) {
        $employeeTimeSheets = self::initiateQuery();
        if (!empty($userIds)) {
            $employeeTimeSheets->whereIn('user_id', $userIds);
        }
        if (!empty($fromDate)) {
            $employeeTimeSheets->where('created_at', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $employeeTimeSheets->where('created_at', '<', Carbon::parse($toDate)->addDay());
        }
        return $employeeTimeSheets;
    }
}
