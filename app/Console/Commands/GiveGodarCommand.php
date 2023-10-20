<?php

namespace App\Console\Commands;

use App\Jobs\GiveGodarJob;
use App\Models\Product;
use App\Repositories\OrderRepo;
use App\Repositories\ProductRepository;
use Illuminate\Console\Command;

class GiveGodarCommand extends Command
{

    protected $signature = 'alaaTv:giveGodarGifts';

    protected $description = 'Give godar product to abrisham tak customers';

    private $orders;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $orders = $this->abrishamiOrders();

        $bar = $this->output->createProgressBar($orders->count());

        if (!$this->confirm("{$orders->count()} orders found, do you want to continue?")) {
            return 0;
        }

        $bar->start();
        foreach ($orders as $order) {
            $bar->advance();
            dispatch(new GiveGodarJob($order, true));
        }

        $bar->finish();

        $this->info("\nRun 'php artisan queue work' or 'sail artisan queue work' to dispatch queued jobs\n");

        return 0;
    }

    private function abrishamiOrders()
    {
        return OrderRepo::getUserCompletedPaidOrders(null, ['orderproducts'])
            ->whereHas('orderproducts', function ($q) {
                $q->whereIn('product_id',
                    ProductRepository::getProductsById(Product::ALL_SINGLE_ABRISHAM_PRODUCTS)->pluck('id')->toArray());
            })->get();
    }
}
