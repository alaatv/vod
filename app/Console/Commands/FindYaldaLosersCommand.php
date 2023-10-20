<?php

namespace App\Console\Commands;

use App\Repositories\OrderproductRepo;
use App\Repositories\SubscriptionRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FindYaldaLosersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:yalda:losers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find users who had mistake in use of yalda discounts';

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
     * @return int
     */
    public function handle()
    {
        $discountWithOrderProducts = $this->getDiscountOrderProductsPairs();

        $orderProductsCollection = $this->getOrderProducts($discountWithOrderProducts);

        if (!$this->confirm("{$orderProductsCollection->count()} items found, Do you want to continue?")) {
            return 0;
        }

        $bar = $this->output->createProgressBar($orderProductsCollection->count());
        $bar->start();
        foreach ($orderProductsCollection as $orderProduct) {
            Log::channel('yalda_subscription')->info('userId: '.$orderProduct->order->user->id.' In order: '.$orderProduct->order_id);
            $bar->advance();
        }
        $bar->finish();

        return 0;
    }

    private function getDiscountOrderProductsPairs(): array
    {
        return SubscriptionRepo::usedYaldaSubscriptions()
            ->pluck('values.discount.orderproduct_id', 'values.discount.discount_amount')
            ->toArray();
    }

    private function getOrderProducts($discountForOrderProducts): Collection
    {
        $orderProductsCollection = collect();
        foreach ($discountForOrderProducts as $discount => $orderProducts) {
            $orderProducts = OrderproductRepo::findItemsWithWorngDiscount($orderProducts, $discount, ['order'])->get();
            $orderProductsCollection->push($orderProducts);
        }

        return $orderProductsCollection->flatten();
    }

}
