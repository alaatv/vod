<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class FillContentIncomeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:seed:contentIncome';

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
        $orders = Order::query()
            ->whereIn('orderstatus_id', Order::getDoneOrderStatus())
            ->whereIn('paymentstatus_id',
                [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
            ->orderBy('id', 'desc')
            ->get();

        $ordersCount = $orders->count();

        $this->info('Inserting content incomes ...');
        $progressBar = new ProgressBar($this->output, $ordersCount);
        $progressBar->start();

        foreach ($orders as $order) {
//            ContentInComeJob::dispatch($order);
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info('Data creation completed!');
    }
}
