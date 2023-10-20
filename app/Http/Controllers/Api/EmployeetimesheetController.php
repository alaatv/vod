<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmEmployeeOvertimeRequest;
use App\Repositories\EmployeeTimesheetRepository;
use Illuminate\Http\Response;

class EmployeetimesheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.CONFIRM_EMPLOYEE_OVER_TIME'),
            ['only' => 'confirmEmployeeOverTime']);
    }

    public function confirmEmployeeOverTime(ConfirmEmployeeOvertimeRequest $request)
    {
        $employeeTimeSheets = EmployeeTimesheetRepository::getEmployeeTimeSheets($request->input('user_ids'),
            $request->input('from'), $request->input('to'))
            ->get();

        $params = ['overtime_status_id' => $request->input('overtime_status_id')];
        foreach ($employeeTimeSheets as $employeeTimeSheet) {
            $employeeTimeSheet->update($params);
        }

        return response()->json(['message', 'اضافه کاری کارمندان با موفقیت به روز رسانی شد.'], Response::HTTP_OK);
    }
}
