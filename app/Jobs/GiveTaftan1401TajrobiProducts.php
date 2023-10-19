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

class GiveTaftan1401TajrobiProducts implements ShouldQueue
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

        $tajrobiPack = $orderproducts->where('product_id', Product::TAFTAN1401_TAJROBI_PACKAGE)->first();

        if (!isset($tajrobiPack)) {
            return null;
        }

        $products = ProductRepository::getProductsById([
            Product::TAFTAN1401_ADABIYAT, Product::TAFTAN1401_ARABI, Product::TAFTAN1401_DINI,
            Product::TAFTAN1401_ZABAN, Product::TAFTAN1401_SHIMI, Product::TAFTAN1401_ZIST,
            Product::TAFTAN1401_RIYAZI_TAJROBI, Product::TAFTAN1401_FIZIK_TAJROBI
        ])->get();

        $zistPrice = Arr::get($products->where('id', Product::TAFTAN1401_ZIST)->first()->price, 'base', 0);
        $shimiPrice = Arr::get($products->where('id', Product::TAFTAN1401_SHIMI)->first()->price, 'base', 0);
        $RTPrice = Arr::get($products->where('id', Product::TAFTAN1401_RIYAZI_TAJROBI)->first()->price, 'base', 0);
        $adabiyatPrice = Arr::get($products->where('id', Product::TAFTAN1401_ADABIYAT)->first()->price, 'base', 0);
        $arabiPrice = Arr::get($products->where('id', Product::TAFTAN1401_ARABI)->first()->price, 'base', 0);
        $diniPrice = Arr::get($products->where('id', Product::TAFTAN1401_DINI)->first()->price, 'base', 0);
        $zabanPrice = Arr::get($products->where('id', Product::TAFTAN1401_ZABAN)->first()->price, 'base', 0);
        $fizikPrice = Arr::get($products->where('id', Product::TAFTAN1401_FIZIK_TAJROBI)->first()->price, 'base', 0);

        $orderproducts->where('product_id',
            Product::TAFTAN1401_RIYAZI_TAJROBI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::TAFTAN1401_RIYAZI_TAJROBI, $RTPrice, $RTPrice, 0, 0, $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::TAFTAN1401_ZIST)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::TAFTAN1401_ZIST, $zistPrice, $zistPrice, 0, 0, $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::TAFTAN1401_SHIMI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::TAFTAN1401_SHIMI, $shimiPrice, $shimiPrice, 0, 0, $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::TAFTAN1401_ADABIYAT)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::TAFTAN1401_ADABIYAT, $adabiyatPrice, $adabiyatPrice, 0, 0,
            $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::TAFTAN1401_ARABI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::TAFTAN1401_ARABI, $arabiPrice, $arabiPrice, 0, 0, $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::TAFTAN1401_DINI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::TAFTAN1401_DINI, $diniPrice, $diniPrice, 0, 0, $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::TAFTAN1401_ZABAN)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::TAFTAN1401_ZABAN, $zabanPrice, $zabanPrice, 0, 0, $tajrobiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::TAFTAN1401_FIZIK_TAJROBI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::TAFTAN1401_FIZIK_TAJROBI, $fizikPrice, $fizikPrice, 0, 0,
            $tajrobiPack->includedInInstalments) : null;

        Cache::tags([
            'userAsset_'.$this->order->user_id,
            'user_'.$this->order->user_id.'_closedOrders',
        ])->flush();

    }
}
