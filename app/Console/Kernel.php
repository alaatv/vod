<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [//
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cache:prune-stale-tags')->hourly();
        /*$schedule->command('telescope:prune --hours=48')
            ->daily();*/
        $schedule->command('horizon:snapshot')
            ->everyFiveMinutes();

//        $schedule->command('backup:mysql-dump')
//            ->timezone('Asia/Tehran')
//            ->dailyAt('04:30');

//        $schedule->command('alaaTv:employee:send:timeSheet 0')
//            ->dailyAt('23:45')
//            ->timezone('Asia/Tehran');
//
//        $schedule->command('alaaTv:employee:check:overtime:confirmation')
//            ->dailyAt('00:05')
//            ->timezone('Asia/Tehran');

        $schedule->command('check:orders --interval=1')
            ->dailyAt('02:00')
            ->timezone('Asia/Tehran');


//        $schedule->command('fromAlaaToDana:sync')
//            ->dailyAt('02:30')
//            ->timezone('Asia/Tehran');
        $schedule->command('dana:sync')
            ->dailyAt('03:00')
            ->timezone('Asia/Tehran');
        $schedule->command('dana:sync:check')
            ->dailyAt('03:45')
            ->timezone('Asia/Tehran');

        $schedule->command('check:orderExam --interval=1')
            ->dailyAt('04:00')
            ->timezone('Asia/Tehran');

        $now = Carbon::now('Asia/Tehran')->subHour();
        $from = $now->subDays(2);
        $schedule->command('alaaTv:calculate:opShare "'.$from.'" "'.$now.'" --calc=2')
            ->dailyAt('05:00')
            ->timezone('Asia/Tehran');

        $schedule->command('alaatv:bonyad:consultant:check 1838043')
            ->dailyAt('06:00')
            ->timezone('Asia/Tehran');

        $schedule->command('alaatv:bonyadExcel:remove')
            ->dailyAt('06:30')
            ->timezone('Asia/Tehran');

        $schedule->command('alaaTv:check:transactions --hasScheduled=1 --accept=1')
            ->dailyAt('07:00')
            ->timezone('Asia/Tehran');

        $schedule->command('alaaTv:check:orderProducts --hasScheduled=1 --from="2022-08-01"')
            ->dailyAt('07:30')
            ->timezone('Asia/Tehran');

        $fromLast9Hours = $now->subHours(9);
        $schedule->command('alaaTv:calculate:opShare "'.$fromLast9Hours.'" "'.$now.'" --calc=2')
            ->dailyAt('16:00')
            ->timezone('Asia/Tehran');

        $schedule->command('alaa-tv:abrisham-2:generate-report')
            ->dailyAt('02:00')
            ->timezone('Asia/Tehran');

        // Hourly
        $schedule->command('alaaTv:check:danaToken')
            ->everyFiveMinutes()
            ->timezone('Asia/Tehran');

        $schedule->command('alaaTv:check:orderProducts --hasScheduled=1 --from="'.$now.'"')
            ->hourly()
            ->timezone('Asia/Tehran');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
