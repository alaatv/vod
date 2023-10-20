<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Orderproduct;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class ExpireOrderssCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:expire:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expiring desired orders';

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
        $orders = Order::query()->whereDoesntHave('coupon', function ($q) {
            $q->where('name', 'بنیاد احسان');
        });
        $progressBar = new ProgressBar($this->output, $orders->count());
        $progressBar->start();
        $orders->each(function ($order) use ($progressBar) {
            $orderproducts = $order->orderproducts;
            /** @var Orderproduct $orderproduct */
            foreach ($orderproducts as $orderproduct) {
                $orderproduct->expire_at = Carbon::now();
                $orderproduct->updateWithoutTimestamp();

                if ($order->completed_at < '2020-03-20 00:00:00' && $orderproduct->product?->category == 'جزوه') {
                    continue;
                }

                if ($order->completed_at < '2020-03-20 00:00:00') {
                    $lockReason_id = 1;
                } else {
                    if ($orderproduct->orderproducttype_id =
                        config('constants.ORDER_PRODUCT_GIFT') || $orderproduct->discountPercentage == 100) {
                        $lockReason_id = 3;
                    } else {
                        if ($order->has('coupon', function ($q) {
                            $q->where('name', 'پرچم');
                        })) {
                            $lockReason_id = 2;
                        }
                    }
                }

                $orderproduct->lockReasons()->attach($lockReason_id);
            }
            $progressBar->advance();
        });

        $progressBar->finish();
    }
}
