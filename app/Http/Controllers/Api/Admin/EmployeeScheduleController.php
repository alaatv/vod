<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeScheduleResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Dayofweek;
use App\Models\Dayofweek;
use App\Models\Employeeschedule;
use App\Models\Employeeschedule;
use App\Models\Employeetimesheet;
use App\Models\Employeetimesheet;
use App\Models\User;
use App\Traits\DateTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Validator;

/**
 * Class EmployeeScheduleController.
 * For Api Version 2.
 * For Admin side.
 *
 * @package App\Http\Controllers\Api\Admin
 */
class EmployeeScheduleController extends Controller
{
    use DateTrait;

    public function __construct()
    {
        $this->middleware('role:'.config('constants.ROLE_ADMIN'), ['only' => ['batchUpdate', 'index'],]);
        $this->middleware('permission:'.config('constants.STORE_EMPLOYEE_SCHEDULE'), ['only' => ['store']]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse|ResourceCollection
     */
    public function index(Request $request)
    {
        if (!isset($request->user_id) || !User::find($request->user_id)) {
            return response()->json(['message' => 'کاربر نامعتبر!'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $schedules = Employeeschedule::where('user_id', $request->user_id)->get();

        return EmployeeScheduleResource::collection($schedules);
    }

    public function batchUpdate(Request $request)
    {
        Validator::make($request->all(), [
            'employee_id' => ['required', 'int', 'min:1'],
            'since' => ['required', 'string', 'date'],
            'till' => ['required', 'string', 'date'],
            'week_data' => ['required', 'array']
        ])->validate();

        $since = $request->get('since');
        $till = $request->get('till');
        $employeeId = $request->get('employee_id');
        $newSchedules = collect($request->get('week_data'));

        $oldSchedules = Employeeschedule::query()->where('user_id', $employeeId)->get();

        $days = $this->getDayOfWeek();

        foreach ($newSchedules as $newSchedule) {
            $dayId = $days [$newSchedule['day']];
            /** @var Employeeschedule $oldSchedule */
            $oldSchedule = $oldSchedules->where('day_id', $dayId)->first();

            if (isset($oldSchedule)) {

                $oldSchedule->update([
                    'beginTime' => $newSchedule['begin'],
                    'finishTime' => $newSchedule['end'],
                ]);
                continue;
            }

            Employeeschedule::create([
                'user_id' => $employeeId,
                'day_id' => $dayId,
                'beginTime' => $newSchedule['begin'],
                'finishTime' => $newSchedule['end'],
                'lunchBreakInSeconds' => 2400
            ]);

            continue;
        }

        $timeSheets = Employeetimesheet::where('user_id', $employeeId)
            ->where('date', '>=', $since)
            ->where('date', '<=', $till)
            ->get();

        /** @var Employeetimesheet $timeSheet */
        foreach ($timeSheets as $timeSheet) {
            $persianDayOfWeek = $this->convertToJalaliDay(Carbon::parse($timeSheet->getRawOriginal('date'))->englishDayOfWeek);
            $dayId = $days [$persianDayOfWeek];
            $newSchedule = $newSchedules->where('day_id', $dayId)->first();

            if (!isset($newSchedule)) {
                Log::error('No new schedule found for '.$persianDayOfWeek);
                continue;
            }

            $timeSheet->update([
                'userBeginTime' => $newSchedule['begin'],
                'userFinishTime' => $newSchedule['end']
            ]);
        }

        return response()->json(['message' => 'Schedule updated successfully']);
    }

    public function getDayOfWeek(): array
    {
        $dayOfWeek = Dayofweek::all();
        $days = [];

        foreach ($dayOfWeek as $day) {
            $days[$day->display_name] = $day->id;
        }

        return $days;
    }

    /**
     * @param  Request|Employeeschedule  $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'employee_id' => ['required', 'integer', 'min:1'],
            'week_data' => ['required', 'array'],
            'week_data.*' => ['required', 'array'],
            'week_data.*.day' => ['required', 'string', 'min:1'],
            'week_data.*.begin' => ['required', 'date_format:H:i'],
            'week_data.*.end' => ['required', 'date_format:H:i'],
        ])->validate();

        if (Employeeschedule::where('user_id', $request->employee_id)->exists()) {
            return response()->json(['message' => 'برای این کاربر قبلا شیفت کاری ثبت شده است!'],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $days = $this->getDayOfWeek();

        try {
            foreach ($request->week_data as $newSchedule) {
                $dayId = $days [$newSchedule['day']];
                Employeeschedule::create([
                    'user_id' => $request->employee_id,
                    'day_id' => $dayId,
                    'beginTime' => $newSchedule['begin'],
                    'finishTime' => $newSchedule['end'],
                    'lunchBreakInSeconds' => Employeeschedule::LUNCH_BREAK_IN_SECONDS,
                ]);
            }
        } catch (Exception $exception) {
            return response()->json(['message' => 'خطای پایگاه داده!', 'errorInfo' => $exception],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(['message' => 'شیفت کاری با موفقیت ثبت شد.'], Response::HTTP_OK);
    }
}
