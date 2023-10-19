<?php

namespace App\Console\Commands;

use App\Imports\RiyaziStudyPlansImport;
use App\Imports\TajrobiStudyPlansImport;
use App\Models\Major;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class InsertStudyPlanByExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:import:studyPlan {majorId} {excelName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register study plane by excel';

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
     */
    public function handle()
    {
        $ext = '.xlsx';
        $majorId = $this->argument('majorId');
        $excelName = $this->argument('excelName');
        $excelName = explode($ext, $excelName)[0];

        $excelPath = storage_path('app/public/general/').$excelName.$ext;

        switch ($majorId) {
            case Major::RIYAZI:
                Excel::import(new RiyaziStudyPlansImport(), $excelPath);
                break;
            case Major::TAJROBI:
                Excel::import(new TajrobiStudyPlansImport(), $excelPath);
                break;
            default:
                break;
        }

        $this->info('Done!');
    }
}
