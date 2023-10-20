<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckInstallmentOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:check:installmentOrders {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check installment orders';

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
        $from = $this->option('from');
        $to = $this->option('to');

        $orders = Order::whereIn('paymentstatus_id',
            [config('constants.PAYMENT_STATUS_INDEBTED'), config('constants.PAYMENT_STATUS_UNPAID')])
            ->whereDoesntHave('orderproducts', function ($q) {
                $q->where('product_id', Product::CUSTOM_DONATE_PRODUCT);
            });

        if (!empty($from)) {
            $orders->where('completed_at', '>=', $from);
        }

        if (!empty($to)) {
            $orders->where('completed_at', '<=', $to);
        }

        $orders = $orders->get();
        $orderCount = $orders->count();

        $this->info('Number of items available: '.$orderCount);

        if (!$this->confirm('Do you wish to continue?', true)) {
            return 0;
        }

        $bar = $this->output->createProgressBar($orderCount);
        $bar->start();
        $this->info('');
        $counter = 0;
        foreach ($orders as $order) {
            /** @var Order $order */

            if ($order->totalCost() != $order->totalPaidCost()) {
                $bar->advance();
                continue;
            }
            $counter++;

            $this->info(' '.$counter.' => '.action('Web\OrderController@edit', $order));
            $this->info('');
            Log::channel('checkInstallmentOrders')->warning($order->id.' : The installment order '.$order->id.' found.');

            $bar->advance();
        }
        $bar->finish();
        $this->info('');

        if ($counter == 0) {
            $this->info('No corrupted orders found');
        }
        $this->info('Done!');
        $this->info('');
    }
}
