<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateOPShareCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:calculate:opShare {from} {to} {--new=} {--calc=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculating op shares';

    public function handle(BillingService $billingService)
    {
        $from = $this->argument('from');
        $from = Carbon::parse($from);
        $to = $this->argument('to');
        $to = Carbon::parse($to)->addDay()->subMicrosecond();
        $new = $this->option('new');
        $calc = $this->option('calc');
        if (is_null($calc) || $calc == 2) {
            $billingService->fillBillingTable(null, $from, $to, $new);
        }
        if (($calc == 1 || $calc == 2)) {
            $billings = $billingService->getBillingBuilder(null, $from, $to)->whereNull('op_share_amount')->get();
            do {
                $c = is_null($billings) ? 0 : $billings->count();
                $billingService->calculateOPshareAmountForBillingTable(null, $from, $to);
                $billings = $billingService->getBillingBuilder(null, $from, $to)->whereNull('op_share_amount')->get();
            } while (!is_null($billings) && $billings->count() != $c);
        }
    }
}
