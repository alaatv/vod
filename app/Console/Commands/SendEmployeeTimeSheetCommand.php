<?php

namespace App\Console\Commands;

use App\Models\Employeeschedule;
use App\Models\Employeetimesheet;
use App\Models\User;
use App\Notifications\EmployeeTimeSheetNotification;
use App\Traits\DateTrait;
use App\Traits\User\EmployeeTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SendEmployeeTimeSheetCommand extends Command
{
    use DateTrait;
    use EmployeeTrait;

    private const HOLIDAY = [
        '2022-03-21',
        '2022-03-22',
        '2022-03-23',
        '2022-03-24',
        '2022-04-02',
        '2022-04-23',
        '2022-05-02',
        '2022-05-03',
        '2022-05-26',
        '2022-06-04',
        '2022-06-05',
        '2022-07-10',
        '2022-07-18',
        '2022-08-07',
        '2022-08-08',
        '2022-09-17',
        '2022-09-25',
        '2022-09-27',
        '2022-10-05',
        '2022-12-27',
        '2023-02-04',
        '2023-02-11',
        '2023-02-18',
        '2023-03-08',
        '2023-03-20',
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:employee:send:timeSheet {employee : The ID of the employee} {--date=}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculates time sheets of employees';
    private $date;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $employeeId = (int) $this->argument('employee');
        $this->date = $this->option('date');
        if ($employeeId > 0) {
            try {
                $user = User::findOrFail($employeeId);
            } catch (ModelNotFoundException $exception) {
                $this->error($exception->getMessage());

                return 0;
            }
            if ($this->confirm('You have chosen '.$user->full_name.'. Do you wish to continue?', true)) {
                $this->performTimeSheetTaskForAnEmployee($user);
            }
        } else {
            $this->performTimeSheetTaskForAllEmployee();
        }

        return false;
    }

    private function performTimeSheetTaskForAnEmployee(User $user)
    {
        $this->info('send TimeSheet to'.$user->full_name);
        $dayOfWeekJalali = $this->convertToJalaliDay(isset($this->date) ? Carbon::parse($this->date)->format('l') : Carbon::today('Asia/Tehran')->format('l'));
        $toDayDate = isset($this->date) ? Carbon::parse($this->date)->format('Y-m-d') : Carbon::today('Asia/Tehran')->format('Y-m-d');
        $this->calculate($user, $dayOfWeekJalali, $toDayDate);
    }

    private function calculate(User $employee, $dayOfWeekJalali, $toDayDate)
    {
        $employeeTimeSheet = Employeetimesheet::where('user_id', $employee->id)
            ->where('date', $toDayDate)
            ->first();
        $done = false;

        $employeeSchedule = Employeeschedule::where('user_id', $employee->id)
            ->whereRelation('dayOfWeek', 'display_name', '=', $dayOfWeekJalali)
            ->first();

        if (!isset($employeeTimeSheet) && $employeeSchedule) {
            $newEmplployeeTimeSheet = $this->createEmplployeeTimeSheet($employeeSchedule, $employee, $toDayDate);

            if ($newEmplployeeTimeSheet->save()) {
                $realWorkTime = $newEmplployeeTimeSheet->obtainRealWorkTime('IN_SECONDS');
                $done = $newEmplployeeTimeSheet->id;
            } else {
                $done = false;
            }
        } else {
            if (isset($employeeTimeSheet)) {
                // set 00:00:00 clockIn base beginLunchBreak, finishLunchBreak, clockOut
                if (strcmp($employeeTimeSheet->clockIn, '00:00:00') == 0) {
                    if (strcmp($employeeTimeSheet->beginLunchBreak, '00:00:00') != 0) {
                        $employeeTimeSheet->clockIn = $employeeTimeSheet->beginLunchBreak;
                    } else {
                        if (strcmp($employeeTimeSheet->finishLunchBreak, '00:00:00') != 0) {
                            $employeeTimeSheet->clockIn = $employeeTimeSheet->finishLunchBreak;
                        } else {
                            if (strcmp($employeeTimeSheet->clockOut, '00:00:00') != 0) {
                                $employeeTimeSheet->clockIn = $employeeTimeSheet->clockOut;
                            }
                        }
                    }
                }

                // set 00:00:00 clockOut base finishLunchBreak, beginLunchBreak, clockIn
                if (strcmp($employeeTimeSheet->clockOut, '00:00:00') == 0) {
                    if (strcmp($employeeTimeSheet->finishLunchBreak, '00:00:00') != 0) {
                        $employeeTimeSheet->clockOut = $employeeTimeSheet->finishLunchBreak;
                    } else {
                        if (strcmp($employeeTimeSheet->beginLunchBreak, '00:00:00') != 0) {
                            $employeeTimeSheet->clockOut = $employeeTimeSheet->beginLunchBreak;
                        } else {
                            if (strcmp($employeeTimeSheet->clockIn, '00:00:00') != 0) {
                                $employeeTimeSheet->clockOut = $employeeTimeSheet->clockIn;
                            }
                        }
                    }
                }

                // set 00:00:00 beginLunchBreak to clockIn if you have finishLunchBreak and clockIn
                if (strcmp($employeeTimeSheet->beginLunchBreak, '00:00:00') == 0
                    && strcmp($employeeTimeSheet->finishLunchBreak, '00:00:00') != 0
                    && strcmp($employeeTimeSheet->clockIn, '00:00:00') != 0) {
                    $employeeTimeSheet->beginLunchBreak = $employeeTimeSheet->clockIn;
                }

                // set 00:00:00 finishLunchBreak to clockOut if you have beginLunchBreak and clockOut
                if (strcmp($employeeTimeSheet->finishLunchBreak, '00:00:00') == 0
                    && strcmp($employeeTimeSheet->beginLunchBreak, '00:00:00') != 0
                    && strcmp($employeeTimeSheet->clockOut, '00:00:00') != 0) {
                    $employeeTimeSheet->finishLunchBreak = $employeeTimeSheet->clockOut;
                }

                if (in_array($toDayDate, self::HOLIDAY)) {
                    $employeeTimeSheet->managercomment = 'ثبت توسط سیستم : تعطیلی رسمی به همراه اضافه کاری';
                    $workTime = explode(':', $employeeTimeSheet->obtainRealWorkTime('HOUR_FORMAT'));
                    $employeeTimeSheet = $this->setEmployeeSchedule($employeeTimeSheet, $employeeSchedule);
                    $employeeTimeSheet->clockOut = $this->calculateRealWorkTime($employeeTimeSheet, $workTime);
                    $employeeTimeSheet->clockIn = '00:00:00';
                }
//            $employeeTimeSheet->managerComment = $employeeTimeSheet->managerComment . " ثبت توسط سیستم : مرخصی یا تعطیلی غیر رسمی";
                $employeeTimeSheet->timeSheetLock = 1;

                $realWorkTime = $employeeTimeSheet->obtainRealWorkTime('IN_SECONDS');
                if ($realWorkTime <= 0) {
                    $employeeTimeSheet->overtime_status_id = config('constants.EMPLOYEE_OVERTIME_STATUS_CONFIRMED');
                }

                if ($employeeTimeSheet->update()) {
                    $done = $employeeTimeSheet->id;
                } else {
                    $done = false;
                }
            }
        }
        if (!$done) {
            return null;
        }
        $employeeTimeSheet = Employeetimesheet::all()
            ->where('id', $done)
            ->first();
        /**
         * Sending auto generated password through SMS
         */

        $todayJalaliDate = $this->convertDate($toDayDate, 'toJalali');
        $todayJalaliDate = explode('/', $todayJalaliDate);
        $jalaliYear = $todayJalaliDate[0];
        $jalaliMonth = $this->convertToJalaliMonth($todayJalaliDate[1]);
        $jalaliDay = $todayJalaliDate[2];
        $jalaliYear = substr($jalaliYear, -2);
        $todayJalaliDateCaption = $jalaliDay.' '.$jalaliMonth.' '.$jalaliYear;
        $persianShiftTime = $employeeTimeSheet->obtainShiftTime('PERSIAN_FORMAT');

        if ($persianShiftTime === 0) {
            return;
        }
        $date = $todayJalaliDateCaption;
        $in = $employeeTimeSheet->clockIn;
        $out = $employeeTimeSheet->clockOut;
        $movazafi = $persianShiftTime;
        $ezafe = $employeeTimeSheet->obtainWorkAndShiftDiff('HOUR_FORMAT');
        $employeeTimeSheet->user->notify(new EmployeeTimeSheetNotification($date, $in, $out, $movazafi, $ezafe));

    }

    private function createEmplployeeTimeSheet($employeeSchedule, $employee, $toDayDate)
    {
        $newEmplployeeTimeSheet = new Employeetimesheet();

        $newEmplployeeTimeSheet->date = $toDayDate;
        $newEmplployeeTimeSheet->user_id = $employee->id;
        $newEmplployeeTimeSheet->beginLunchBreak = '00:00:00';
        $newEmplployeeTimeSheet->finishLunchBreak = '00:00:00';
        $newEmplployeeTimeSheet->breakDurationInSeconds = 0;
        $newEmplployeeTimeSheet->employeeComment = null;
        $newEmplployeeTimeSheet->timeSheetLock = 1;
        $newEmplployeeTimeSheet->isPaid = 1;
        $newEmplployeeTimeSheet->workdaytype_id = 1;
        $newEmplployeeTimeSheet->userBeginTime = $employeeSchedule->getRawOriginal('beginTime');
        $newEmplployeeTimeSheet->userFinishTime = $employeeSchedule->getRawOriginal('finishTime');
        $newEmplployeeTimeSheet->allowedLunchBreakInSec = $employeeSchedule->getRawOriginal('lunchBreakInSeconds');

        if (in_array($toDayDate, self::HOLIDAY)) {
            $newEmplployeeTimeSheet->clockIn = $employeeSchedule->getRawOriginal('beginTime');
            $newEmplployeeTimeSheet->clockOut = $employeeSchedule->getRawOriginal('finishTime');
            $newEmplployeeTimeSheet->managerComment = 'ثبت توسط سیستم : تعطیلی رسمی';
        } else {
            $newEmplployeeTimeSheet->clockIn = '00:00:00';
            $newEmplployeeTimeSheet->clockOut = '00:00:00';
            $newEmplployeeTimeSheet->managerComment = 'ثبت توسط سیستم : مرخصی';
        }

        return $newEmplployeeTimeSheet;
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

    private function performTimeSheetTaskForAllEmployee()
    {
        $users = $this->getEmployee();
        $bar = $this->output->createProgressBar($users->count());
        foreach ($users as $user) {
            $this->performTimeSheetTaskForAnEmployee($user);
            $bar->advance();
        }
        $bar->finish();
        $this->info('');
    }
}
