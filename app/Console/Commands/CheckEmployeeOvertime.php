<?php

namespace App\Console\Commands;

use App\Models\Employeetimesheet;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckEmployeeOvertime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:employee:check:overtime:confirmation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks employees overtime confirmation';

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

        $workTimeSheets =
            Employeetimesheet::where('overtime_status_id',
                config('constants.EMPLOYEE_OVERTIME_STATUS_UNCONFIRMED'))->get();
        $workTimeSheetCount = $workTimeSheets->count();

        if (!$this->confirm('There are '.$workTimeSheetCount.' unconfirmed overtime sheets. Do you want to proceed rejecting?',
            true)) {
            return 0;
        }
        $this->comment('Proceeding work time sheets...');
        $now = Carbon::now();
        $rejectedTimeSheetCount = 0;

        $bar = $this->output->createProgressBar($workTimeSheetCount);
        foreach ($workTimeSheets as $workTimeSheet) {
            if (optional($workTimeSheet->user)->isEliteDeveloper()) {
                $workTimeSheet->update([
                    'overtime_status_id' => config('constants.EMPLOYEE_OVERTIME_STATUS_CONFIRMED'),
                ]);
                $bar->advance();
                continue;
            }
            $splitedDate = explode('-', $workTimeSheet->getRawOriginal('date'));
            $timePoint = Carbon::createMidnightDate($splitedDate[0], $splitedDate[1], $splitedDate[2])->addDay();
            if ($now->diffInMinutes($timePoint) < 1440) {
                $bar->advance();
                continue;
            } // = 24 hours
            $updateResult = $workTimeSheet->update([
                'overtime_status_id' => config('constants.EMPLOYEE_OVERTIME_STATUS_REJECTED'),
            ]);
            if ($updateResult) {
                $rejectedTimeSheetCount++;
            }

            $bar->advance();
        }
        $bar->finish();
        $this->info($rejectedTimeSheetCount.' overtimes rejected out of total '.$workTimeSheetCount);
        $this->comment('Process completed successfully!');
    }
}
