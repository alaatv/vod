<?php

namespace App\Console\Commands;

use App\Models\Dayofweek;
use App\Models\Employeeschedule;
use App\Models\Employeetimesheet;
use App\Traits\DateTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;

class InsertTimeSheetCommand extends Command
{

    use DateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:employee:insert:timesheet {employeeID : the ID of the employee} {from : start date} {--end=} {--mode=insertion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executing a general code';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle2()
    {
        $timesheets = Employeetimesheet::where('user_id', 925019)->where('clockIn', '!=', '09:00:00')->where('clockIn',
            '!=', '00:00:00')->where('userBeginTime', '!=', '00:00:00')->where('date', '<', '2021-02-28')->where('date',
            '!=', '2020-12-21')->get();
        $timesheetCount = $timesheets->count();

        if (!$this->confirm("$timesheetCount found , Do you wish to continue?", true)) {
            return 0;
        }

        $progressBar = new ProgressBar($this->output, $timesheetCount);
        $progressBar->start();

        foreach ($timesheets as $timesheet) {
            $clockOut = Carbon::parse($timesheet->clockOut);
            $newClockOut = $clockOut->addMinutes(105);
            $timesheet->update([
                'clockOut' => $newClockOut->toTimeString(),
            ]);
        }

        $progressBar->finish();


    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = $this->argument('employeeID');
        $startDate = $this->argument('from');
        $endDate = $this->option('end');
        $mode = $this->option('mode');

        $schedules = Employeeschedule::where('user_id', $userId)->get();

        $dateIterator = Carbon::parse($startDate);
        $endDate = isset($endDate) ? Carbon::parse($endDate) : Carbon::now();

        $this->info('Inserting time sheets ...');

        while (($dateIterator <= $endDate)) {
            $persianDayOfWeek = $this->convertToJalaliDay($dateIterator->englishDayOfWeek);
            $dayId = Dayofweek::where('display_name', $persianDayOfWeek)->first()->id;
            $dateString = $dateIterator->toDateString();
            $this->info($dateString);

            $schedule = $schedules->dayId($dayId)->first();
            if (!isset($schedule)) {
                Log::channel('debug')->warning("GeneralCommand: user $userId does not have schedule for date ".$dateString);
                $dateIterator = $dateIterator->addDay();
                continue;
            }

            $existedTimeSheet = Employeetimesheet::query()->where('date', $dateString)->where('user_id',
                $userId)->where('clockIn', '00:00:00')->first();

            if (!isset($existedTimeSheet) && $mode != 'insertion') {
                $this->info("No time sheet found for $dateString , this date was skipped because mode is not set to insertion");
                $dateIterator = $dateIterator->addDay();
                continue;
            }

            if (!isset($existedTimeSheet)) {
                try {
                    Employeetimesheet::query()->create([
                        'user_id' => $userId,
                        'date' => $dateString,
                        'allowedLunchBreakInSec' => $schedule->getRawOriginal('lunchBreakInSeconds'),
                        'clockIn' => $schedule->getRawOriginal('beginTime'),
                        'beginLunchBreak' => '00:00:00',
                        'finishLunchBreak' => '00:00:00',
                        'clockOut' => $schedule->getRawOriginal('finishTime'),
                        'userBeginTime' => $schedule->getRawOriginal('beginTime'),
                        'userFinishTime' => $schedule->getRawOriginal('finishTime'),
                        'workdaytype_id' => 1,
                        'managerComment' => 'درج از طریق کامند',
                        'overtime_status_id' => 1,
                        'timeSheetLock' => 1,
                    ]);
                } catch (QueryException $e) {
                    Log::channel('debug')->warning("GeneralCommand: Database error on updating time sheet for user $userId for date ".$dateString.' . '.$e->getMessage());
                }

                $dateIterator = $dateIterator->addDay();
                continue;
            }

            if ($mode != 'update') {
                $this->info("One time sheet found for $dateString but this date was skipped because mode is not set to udpate");
                $dateIterator = $dateIterator->addDay();
                continue;
            }

            try {
                $existedTimeSheet->update([
                    'allowedLunchBreakInSec' => $schedule->getRawOriginal('lunchBreakInSeconds'),
                    'clockIn' => $schedule->getRawOriginal('beginTime'),
                    'beginLunchBreak' => '00:00:00',
                    'finishLunchBreak' => '00:00:00',
                    'clockOut' => $schedule->getRawOriginal('finishTime'),
                    'workdaytype_id' => 1,
                    'isPaid' => 1,
                    'managerComment' => 'اصلاح از طریق کامند',
                    'overtime_status_id' => 1,
                    'timeSheetLock' => 1,
                ]);
            } catch (QueryException $e) {
                Log::channel('debug')->warning("GeneralCommand: Database error on updating time sheet for user $userId for date ".$dateString.' . '.$e->getMessage());
            }

            $dateIterator = $dateIterator->addDay();
        }

        $this->info('Done!');
        return null;
    }

    public function handle3()
    {
        $userId = $this->argument('employeeID');
        $startDate = $this->argument('from');
        $endDate = $this->option('end');

        $schedules = Employeeschedule::where('user_id', $userId)->get();

        $dateIterator = Carbon::parse($startDate);
        $endDate = isset($endDate) ? Carbon::parse($endDate) : Carbon::now();

        $exceptions = collect([

        ]);

        $this->info('Inserting time sheets ...');

        while (($dateIterator <= $endDate)) {
            $persianDayOfWeek = $this->convertToJalaliDay($dateIterator->englishDayOfWeek);
            $dayId = Dayofweek::where('display_name', $persianDayOfWeek)->first()->id;
            $dateString = $dateIterator->toDateString();
            $this->info($dateString);

            $schedule = $schedules->dayId($dayId)->first();
            if (!isset($schedule)) {
                Log::channel('debug')->warning("GeneralCommand: user $userId does not have schedule for date ".$dateString);
                $dateIterator = $dateIterator->addDay();
                continue;
            }

            $existedTimeSheet = Employeetimesheet::query()->where('date', $dateString)->where('user_id',
                $userId)->first();
            if (isset($existedTimeSheet)) {
                Log::channel('debug')->warning("GeneralCommand: user $userId had time sheet for date ".$dateString);
                $dateIterator = $dateIterator->addDay();
                continue;
            }

            $clockIn = $exceptions->where('date', $dateString)->isNotEmpty() ? $exceptions->where('date',
                $dateString)->first()['beginTime'] : $schedule->getRawOriginal('beginTime');
            $clockOut = $exceptions->where('date', $dateString)->isNotEmpty() ? $exceptions->where('date',
                $dateString)->first()['finishTime'] : $schedule->getRawOriginal('finishTime');

            try {
                Employeetimesheet::create([
                    'user_id' => $userId,
                    'date' => $dateString,
                    'userBeginTime' => $schedule->getRawOriginal('beginTime'),
                    'userFinishTime' => $schedule->getRawOriginal('finishTime'),
                    'allowedLunchBreakInSec' => $schedule->getRawOriginal('lunchBreakInSeconds'),
                    'clockIn' => $clockIn,
                    'beginLunchBreak' => '00:00:00',
                    'finishLunchBreak' => '00:00:00',
                    'clockOut' => $clockOut,
                    'workdaytype_id' => 1,
                    'isPaid' => 1,
                    'managerComment' => 'ثبت از طریق کامند',
                    'overtime_status_id' => 1,
                    'timeSheetLock' => 1,
                ]);
            } catch (QueryException $e) {
                Log::channel('debug')->warning("GeneralCommand: Database error on creating time sheet for user $userId for date ".$dateString.' . '.$e->getMessage());
            }

            $dateIterator = $dateIterator->addDay();
        }

        $this->info('Done!');
        return null;
    }
}
