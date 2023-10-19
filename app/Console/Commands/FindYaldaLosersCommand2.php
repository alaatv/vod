<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FindYaldaLosersCommand2 extends Command
{

    public const YALDA_DISCOUNT_IDENTIFIER_FIELDS = [
        'name' => ['operator' => 'like', 'value' => 'Yalda1400']
    ];
    protected $signature = 'alaaTv:yalda:losers2';
    protected $description = 'find users who had mistake in use of yalda discounts base coupons';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $orders = $this->getOrders();

        if (!$this->confirm('Do you set YALDA_DISCOUNT_IDENTIFIER_FIELDS const in FindYaldaLosersCommand2 class?')) {
            return 0;
        }

        if (!$this->confirm("{$orders->count()} Orders found, Do you want to continue?")) {
            return 0;
        }

        $bar = $this->output->createProgressBar($orders->count());
        $this->updateOrderProducts($orders, $bar);

        return 0;
    }

    private function getOrders()
    {
        return OrderRepo::filterOrdersBaseCoupon(self::YALDA_DISCOUNT_IDENTIFIER_FIELDS)->get();
    }

    private function updateOrderProducts($orders, $bar)
    {
        $bar->start();
        foreach ($orders as $order) {

            $this->refreshIncludeInCoupon($order);
            /**@var Order $order */
            $order->refreshCost();
            $this->logMistakes($order);

            $bar->advance();
        }
        $bar->finish();
    }

    private function refreshIncludeInCoupon(Order $order)
    {
        $order->orderproducts()
            ->whereIn('product_id', Product::ALL_SINGLE_ABRISHAM_PRODUCTS)
            ->update(['discountPercentage' => 0]);
    }

    private function logMistakes(Order $order)
    {
        $mistake = $order->totalPaidCost() - $order->totalCost();
        if ($mistake > 0) {
            $message = "User: {$order->user->id}, Order: {$order->id} (mistake amount: $mistake)";
            Log::channel('yalda_coupon')->info($message);
        }
    }
}
