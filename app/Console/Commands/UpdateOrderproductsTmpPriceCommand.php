<?php

namespace App\Console\Commands;

use App\Models\Orderproduct;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateOrderproductsTmpPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:update-orderproducts-tmpprice {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculating and updating orderproducts final cost';

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
        $orderproducts = Orderproduct::whereHas('order', function ($q) {
            $q->whereNotIn('orderstatus_id',
                [config('constants.ORDER_STATUS_OPEN', config('constants.ORDER_STATUS_OPEN_DONATE'))]);
        });

        if ($this->confirm('Do you want to process only new orderproducts?', true)) {
            $orderproducts = $orderproducts->whereNull('tmp_final_cost');
        }

        if (!$this->hasArgument('date')) {
            $this->info('please provide date');
            return 0;
        }

        $orderproducts->where('created_at', '>', Carbon::parse($this->argument('date')));

        $orderproducts = $orderproducts->get();

        if (!$this->confirm('Found '.$orderproducts->count().' orderproducts , Do you want to proceed?', true)) {
            $this->info('Done');
            return 0;
        }
        $bar = $this->output->createProgressBar($orderproducts->count());
        foreach ($orderproducts as $orderproduct) {
            /** @var Orderproduct $orderproduct */
            $orderproduct->setTmpFinalCost();
            $bar->advance();
        }
        $bar->finish();

        $this->info('Done');
    }
}
