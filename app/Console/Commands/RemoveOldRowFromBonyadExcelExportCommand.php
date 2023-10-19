<?php

namespace App\Console\Commands;

use App\Models\BonyadEhsanExcelExport;

use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveOldRowFromBonyadExcelExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:bonyadExcel:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove old row of bonyad excel export';

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
     * @return int
     */
    public function handle()
    {
        BonyadEhsanExcelExport::where('created_at', '<', Carbon::now()->subMinutes(30))->delete();
        $this->info('done');
        return 0;
    }
}
