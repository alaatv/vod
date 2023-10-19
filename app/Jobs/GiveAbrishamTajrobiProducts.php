<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderproductRepo;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class GiveAbrishamTajrobiProducts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Order $order
     */
    private $order;

    /**
     * GiveAbrishamTajrobiProducts constructor.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }


    /**
     *
     * /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $orderproducts = $this->order->orderproducts;

        $tajrobiPack = $orderproducts->where('product_id', Product::RAHE_ABRISHAM99_PACK_TAJROBI)->first();

        if (!isset($tajrobiPack)) {
            return null;
        }

        $products = ProductRepository::getProductsById([
            Product::RAHE_ABRISHAM99_ZIST, Product::RAHE_ABRISHAM99_SHIMI, Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI
        ])->get();

        $zistPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM99_ZIST)->first()->price, 'base', 0);
        $shimiPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM99_SHIMI)->first()->price, 'base', 0);
        $RTPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI)->first()->price, 'base',
            0);

        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI, $RTPrice, $RTPrice, 0, 0,
            $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM99_ZIST)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM99_ZIST, $zistPrice, $zistPrice, 0, 0, $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM99_SHIMI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM99_SHIMI, $shimiPrice, $shimiPrice, 0, 0, $tajrobiPack->includedInInstalments) : null;

        Cache::tags([
            'userAsset_'.$this->order->user_id,
            'user_'.$this->order->user_id.'_closedOrders',
        ])->flush();

    }
}
