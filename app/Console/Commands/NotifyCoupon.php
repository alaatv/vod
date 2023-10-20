<?php

namespace App\Console\Commands;

use App\Models\Orderproduct;
use App\Models\Product;
use App\Notifications\PurchasedCoupon2;
use Illuminate\Console\Command;

class NotifyCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:notifyCoupon';

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
        $orderproducts = Orderproduct::query()->where('product_id', Product::COUPON_PRODUCT)
            ->whereHas('order', function ($q2) {
                $q2->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                    ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'));
            })->get();

        $count = $orderproducts->count();
        if (!$this->confirm("$count orderproducts found. Do you wish to continue?", true)) {

            $this->info('Done!');
            return;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($orderproducts as $orderproduct) {
            $order = $orderproduct->order;
            $user = $orderproduct->order->user;
            if (!isset($user)) {
                $this->info('Error: No User found for order '.$order->id);
                $this->info("\n");
                continue;
            }

            $coupon = $orderproduct->purchasedCoupons->first();
            if (!isset($coupon)) {
                $this->info('Error: No coupon found for order '.$order->id);
                $this->info("\n");
                continue;
            }

            $user->notify(new PurchasedCoupon2($coupon));
            $bar->advance();
        }
        $bar->finish();


        $this->info('Done!');
    }
}
