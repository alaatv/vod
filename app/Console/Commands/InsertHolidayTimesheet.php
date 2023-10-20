<?php

namespace App\Console\Commands;

use App\Models\Employeeschedule;
use App\Models\Employeetimesheet;
use App\Repositories\UserRepo;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InsertHolidayTimesheet extends Command
{
    public const HOLIDAYS = [
        '2022-03-21' => 'دوشنبه',
        '2022-03-22' => 'سه شنبه',
        '2022-03-23' => 'چهارشنبه',
        '2022-03-24' => 'پنجشنبه',
        '2022-04-02' => 'شنبه',
    ];
    protected $signature = 'alaaTv:employee:insert:holidayTimesheet';

    protected $description = 'update holydays work time';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // 6 is employee role id
        $employees = UserRepo::withRoles([6])->get();
        $this->info('-------------------UPDATING EMPLOYEE TIME SHEET-------------------', "\n");
        $this->updateWorkSheet($employees);
        return 0;
    }

    private function updateWorkSheet($employees)
    {
        $logArray = [];
        $dayBar = $this->output->createProgressBar(count(self::HOLIDAYS));
        foreach (self::HOLIDAYS as $date => $weekDay) {
            $employeesBar = $this->output->createProgressBar(count($employees));
            foreach ($employees as $employee) {
                $employeeSchedule = Employeeschedule::query()->where('user_id',
                    $employee->id)->whereRelation('dayOfWeek', 'display_name', '=', $weekDay)->first();

                if (!$employeeSchedule) {
                    if (!in_array("{$employee->id}  $weekDay", $logArray)) {
                        $logArray [] = "{$employee->id}  $weekDay";
                    }
                    $employeesBar->advance();
                    continue;
                }

                $userBeginTime = explode(' ', $employeeSchedule->beginTime)[0];
                $userFinishTime = explode(' ', $employeeSchedule->finishTime)[0];

                $employeeTimeSheet = $employee->employeetimesheets()->where('date', $date)->first();
                if ($employeeTimeSheet) {
                    $managerComment = 'ثبت توسط سیستم : تعطیلی رسمی';

                    if ($employeeTimeSheet->clockIn !== '00:00:00' || $employeeTimeSheet->clockOut !== '00:00:00') {
                        $managerComment = 'ثبت توسط سیستم : تعطیلی رسمی به همراه اضافه کاری';
                        $workTime = explode(':', $employeeTimeSheet->obtainRealWorkTime('HOUR_FORMAT'));
                        $employeeTimeSheet = $this->setEmployeeSchedule($employeeTimeSheet, $employeeSchedule);
                        $userFinishTime = $this->calculateRealWorkTime($employeeTimeSheet, $workTime);
                        $userBeginTime = '00:00:00';
                    }
                    $this->update($employeeTimeSheet, $userBeginTime, $userFinishTime, $managerComment);
                    $employeesBar->advance();
                    continue;
                }

                $this->create($employee, $date, $userBeginTime, $userFinishTime);
                $employeesBar->advance();
            }

            $employeesBar->finish();
            $this->info("all employees updated on $date.", "\n");
            $this->info("\n");

            $dayBar->advance();
        }

        $dayBar->finish();
        $this->info("\n");
        $this->info('-------------------UPDATE COMPLETE-------------------', "\n");

        //dd($logArray);
    }

    private function setEmployeeSchedule(
        Employeetimesheet $employeeTimeSheet,
        Employeeschedule $employeeSchedule
    ): Employeetimesheet {
        $employeeTimeSheet->clockIn = $employeeSchedule->getRawOriginal('beginTime');
        $employeeTimeSheet->beginlunchbreak = '00:00:00';
        $employeeTimeSheet->finishlunchbreak = '00:00:00';
        $employeeTimeSheet->clockOut = $employeeSchedule->getRawOriginal('finishTime');

        return $employeeTimeSheet;
    }

    private function calculateRealWorkTime(Employeetimesheet $employeeTimeSheet, array $workTime)
    {
        $realWorkTime = Carbon::parse($employeeTimeSheet->obtainRealWorkTime('HOUR_FORMAT'));
        $realWorkTime->addHours($workTime[0]);
        $realWorkTime->addMinutes($workTime[1]);
        $realWorkTime->addSeconds($workTime[2]);

        return $realWorkTime;
    }

    private function update($employeetimesheet, $userBeginTime, $userFinishTime, $managerComment)
    {

        try {
            $employeetimesheet->update([
                'clockIn' => $userBeginTime,
                'clockOut' => $userFinishTime,
                'managerComment' => $managerComment,
            ]);
        } catch (Exception $exception) {
            Log::error("Database Error on updating time sheet of {$employeetimesheet->user->id}");
        }

    }

    private function create($employee, $date, mixed $userBeginTime, mixed $userFinishTime)
    {
        try {
            Employeetimesheet::query()->create([
                'user_id' => $employee->id,
                'date' => $date,
                'userBeginTime' => $userBeginTime,
                'userFinishTime' => $userFinishTime,
                'allowedLunchBreakInSec' => 2400,
                'clockIn' => $userBeginTime,
                'beginLunchBreak' => '00:00:00',
                'finishLunchBreak' => '00:00:00',
                'clockOut' => $userFinishTime,
                'timeSheetLock' => 1,
                'workdaytype_id' => 1,
                'isPaid' => 1,
                'managerComment' => 'ثبت توسط سیستم : تعطیلی رسمی',
                'overtime_status_id' => 1,
            ]);
        } catch (Exception $exception) {
            Log::error("Database Error on creating time schedule for {$employee->id}");
        }
    }
}
