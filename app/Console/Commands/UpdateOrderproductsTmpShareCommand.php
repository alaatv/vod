<?php

namespace App\Console\Commands;

use App\Models\Orderproduct;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateOrderproductsTmpShareCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:update-orderproducts-tmpshare {date} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates and updates orderproducts share cost';

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
        $from = Carbon::parse($this->argument('date'));
        $to = Carbon::parse($this->option('to'))->addDay()->subMicrosecond();

        $orderproducts =
            Orderproduct::where('orderproducttype_id', 1)->whereHas('order', function ($q) use ($from, $to) {
                $q->whereIn('orderstatus_id',
                    [config('constants.ORDER_STATUS_CLOSED', config('constants.ORDER_STATUS_POSTED'))])
                    ->whereIn('paymentstatus_id',
                        [config('constants.PAYMENT_STATUS_PAID', config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'))])
                    ->where('completed_at', '>=', $from);

                if (isset($to)) {
                    $q->where('completed_at', '<=', $to);
                }

            });

        if ($this->confirm('Do you want to process only new orderproducts?', true)) {
            $orderproducts = $orderproducts->whereNull('tmp_share_order');
        }


        $orderproducts = $orderproducts->get();

        if (!$this->confirm('Found '.$orderproducts->count().' orderproducts , Do you want to proceed?', true)) {
            $this->info('Done');
            return 0;
        }
        $bar = $this->output->createProgressBar($orderproducts->count());
        foreach ($orderproducts as $orderproduct) {
            /** @var Orderproduct $orderproduct */
            $share = $orderproduct->setShareCost();
            $bar->advance();
        }
        $bar->finish();

        $this->info('Done');
    }
}
