<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\ProductAddedToUser;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOrderProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:check:orderProducts {--from=} {--to=} {--hasScheduled=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check order products';

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
        $hasScheduled = (bool) $this->option('hasScheduled');
        $from = $this->option('from');
        $to = $this->option('to');
        $orders = Order::where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'))
            ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'));

        if (!empty($from)) {
            $orders->where('completed_at', '>=', $from);
        }

        $orders->where('completed_at', '<=', $to ?? Carbon::now('Asia/Tehran'));

        $orders = $orders->get();
        $orderCount = $orders->count();

        $this->info('Number of available items: '.$orderCount);

        if (!$hasScheduled && !$this->confirm('Do you wish to continue?', true)) {
            return 0;
        }

        $bar = $this->output->createProgressBar($orderCount);
        $bar->start();
        $counter = 0;
        foreach ($orders as $order) {
            /** @var Order $order */
            $orderTotalCost = (int) $order->obtainOrderCost(true, false)['totalCost'];
            if (($diff = $order->totalPaidCost() - $orderTotalCost) <= Order::VALID_DIFF_TOTAL_PAID_COST_AND_ORDER_TOTAL_COST) {
                $bar->advance();
                continue;
            }

            $counter++;
            $this->info("\n");
            $this->warn($counter.' . TotalPaidCost and orderTotalCost of the order '.$order->id.' do not match.');
            $this->warn('order link => '.action('Web\OrderController@edit', $order));

            Log::channel('checkOrderProducts')->warning('TotalPaidCost and orderTotalCost of the order '.$order->id.' do not match.');

            $trashedOrderProduct = $order->orderproducts()->onlyTrashed()->orderBy('deleted_at', 'desc')->first();
            if (!($trashedOrderProduct && $diff == (1 - ($order->couponDiscount / 100)) * $trashedOrderProduct['price']['final'])) {
                $bar->advance();
                continue;
            }

            $restoreResult = $trashedOrderProduct->restore();

            if (!$restoreResult) {
                $this->error('Error on restoring orderproduct '.$trashedOrderProduct->id.' . Order: '.$order->id);
                Log::channel('checkOrderProducts')->error('Error on restoring orderproduct '.$trashedOrderProduct->id.' . Order: '.$order->id);
                continue;
            }

            $this->info('The orderproduct '.$trashedOrderProduct->id.' was restored. Order : '.$order->id);
            Log::channel('checkOrderProducts')->info('The orderproduct '.$trashedOrderProduct->id.' was restored. Order : '.$order->id);

            optional($order->user)->notify(new ProductAddedToUser(optional($trashedOrderProduct->product)->name));


            $bar->advance();
        }
        $bar->finish();
        $this->info("\n");

        if ($counter == 0) {
            $this->info('No corrupted orders found');
        }
        $this->info('Done!');

        return 0;
    }
}
