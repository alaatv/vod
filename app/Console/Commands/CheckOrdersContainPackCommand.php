<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductRepository;
use App\Services\OrderProductsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CheckOrdersContainPackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:orders {--interval= : filter orders by hour}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Orders that have packages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $interval = $this->option('interval');
        $interrelationProducts = ProductRepository::interrelationProducts();
        $interrelationProductsIds = $interrelationProducts->pluck('id');

        $orders = OrderRepo::ordersHasRelatedProduct($interrelationProductsIds, $interval)->get();
        $bar = $this->output->createProgressBar($orders->count());
        $orders->each(function ($order) use ($interrelationProducts, $interrelationProductsIds, $bar, &$i) {

            $orderProductsPacks = $order->orderproducts->whereIn('product_id', $interrelationProductsIds);

            $orderProductsPacks->each(function ($orderProductPack) use ($order, $interrelationProducts, &$i) {
                $relatedProducts = $interrelationProducts->where('id', $orderProductPack->product_id)
                    ->pluck('productProduct')->flatten();
                OrderProductsService::mapRelatedProductForAddToOrderProduct($relatedProducts, $orderProductPack,
                    $order);
            });
            $bar->advance();
        });

        $bar->finish();
        $this->newLine();
        Artisan::call('cache:clear');
        $this->info($i.'Order Products Created');

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle2()
    {
        $pack_id = $this->argument('pack_id');
        $products_id = $this->argument('products_id');
        $from = $this->option('from');
        $to = $this->option('to');
        $force = $this->option('force');

        if (!in_array($pack_id, Product::ALL_PACK)) {

            $this->info('There is no pack with this id : '.$pack_id);

            return 0;
        }

        $orders = Order::with('orderproducts')
            ->whereRelation('orderproducts', 'product_id', $pack_id)
            ->paidAndClosed();

        if (!empty($from)) {
            $orders->where('completed_at', '>=', $from);
        }

        $orders->where('completed_at', '<=', $to ?? Carbon::now('Asia/Tehran'));

        $orders = $orders->get();

        if ($orders->isEmpty()) {
            $messageSufix = '';

            if ($from || $to) {
                $messageSufix = 'from : '.$from.PHP_EOL.'to : '.$to;
            }

            $this->info('There is no orders contain this pack : '.$pack_id.PHP_EOL.$messageSufix);

            return 0;
        }

        $products_id = explode(',', $products_id);

        if (empty($force)) {
            $incompleteOrders = 0;

            foreach ($orders as $order) {
                if ($this->isOrderproductsComplete($order, $products_id)) {
                    continue;
                }
                $incompleteOrders += 1;
            }

            $this->info('Number of orders that is incomplete : '.$incompleteOrders);

            return 0;
        }

        foreach ($orders as $order) {
            if ($this->isOrderproductsComplete($order, $products_id)) {
                continue;
            }
            $this->completeOrderproducts($order, $products_id);
        }

        $messageSufix = '';

        if ($from || $to) {
            $messageSufix = 'from : '.$from.PHP_EOL.'to : '.$to;
        }

        $this->info('Orderproducts have completed for pack : '.$pack_id.PHP_EOL.$messageSufix);

        return 0;
    }

    public function isOrderproductsComplete(Order $order, array $products_id): bool
    {
        $productsInOrder = $order->orderproducts()->pluck('product_id')->toArray();

        if (empty(array_diff($products_id, $productsInOrder))) {
            return true;
        }

        return false;
    }

    public function completeOrderproducts(Order $order, array $products_id): void
    {
        $productsInOrder = $order->orderproducts()->pluck('product_id')->toArray();

        foreach ($products_id as $product_id) {
            if (in_array($product_id, $productsInOrder)) {
                continue;
            }
            OrderproductRepo::createHiddenOrderproduct($order->id, $product_id, 0, 0);
        }
    }
}
