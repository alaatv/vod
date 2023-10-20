<?php

namespace App\Console\Commands;

use App\Jobs\GiveArashPackProducts;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Traits\User\AssetTrait;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class GiveProductCommand extends Command
{
    use AssetTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:giveProduct';

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
            ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
            ->whereHas('orderproducts', function (Builder $q) {
                $q->whereIn('product_id', [
                    Product::ARASH_PACK_TAJROBI,
                    Product::ARASH_PACK_RIYAZI,
                ]);
            })
            ->where('completed_at', '<', '2021-04-21 00:00:00')
            ->get();

        $orderCount = $orders->count();
        if (!$this->confirm("$orderCount found, Do you want to continue?", true)) {
            return 0;
        }

        $bar = $this->output->createProgressBar($orderCount);
        /** @var Order $order */
        foreach ($orders as $order) {
            /** @var User $user */
            $user = $order->user;
            if (!isset($user)) {
                $this->info('order #'.$order->id.' had no user');
                $bar->advance();
                continue;
            }

            dispatch(new GiveArashPackProducts($order));

            Log::channel('debug')->warning($user->mobile);
            $bar->advance();
        }

        $bar->finish();
        return true;
    }

}
