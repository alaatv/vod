<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Traits\Helper;
use Carbon\Carbon;
use Illuminate\Console\Command;

class sumTransactions extends Command
{
    use Helper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:sumTransactions {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        return 0;
        if (!$this->hasArgument('date')) {
            $this->warn('please set date');
            return 0;
        }

        $fromDate = Carbon::parse($this->argument('date'))->toDateString();
        $untilDate = Carbon::now('Asia/Tehran')->toDateString();

        $transactions = Transaction::orderBy('created_at', 'Desc');

        $this->timeFilterQuery($transactions, $fromDate, $untilDate, 'completed_at')
            ->whereIn('paymentmethod_id', [1, 2])
            ->where('transactionstatus_id', 3)
            ->where('cost', '>', 0);

        $transactions = $transactions->get();

        $totalCost = number_format($transactions->sum('cost'));

        $this->info('total sum:'.$totalCost);
        $this->info("\n");


        return 0;
    }
}
