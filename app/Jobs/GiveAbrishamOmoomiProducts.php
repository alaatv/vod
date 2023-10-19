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

class GiveAbrishamOmoomiProducts implements ShouldQueue
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

        $omoomiPack = $orderproducts->where('product_id', Product::RAHE_ABRISHAM1401_PACK_OMOOMI)->first();

        if (!isset($omoomiPack)) {
            return null;
        }

        $products = ProductRepository::getProductsById([
            Product::RAHE_ABRISHAM1401_ADABIYAT, Product::RAHE_ABRISHAM1401_ARABI, Product::RAHE_ABRISHAM1401_DINI,
            Product::RAHE_ABRISHAM1401_ZABAN
        ])->get();

        $zabanPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM1401_ZABAN)->first()->price, 'base', 0);
        $diniPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM1401_DINI)->first()->price, 'base', 0);
        $arabiPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM1401_ARABI)->first()->price, 'base', 0);
        $adabiyatPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM1401_ADABIYAT)->first()->price, 'base',
            0);


        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM1401_ZABAN)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM1401_ZABAN, $zabanPrice, $zabanPrice, 0, 0,
            $omoomiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM1401_DINI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM1401_DINI, $diniPrice, $diniPrice, 0, 0, $omoomiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM1401_ARABI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM1401_ARABI, $arabiPrice, $arabiPrice, 0, 0,
            $omoomiPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM1401_ADABIYAT)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM1401_ADABIYAT, $adabiyatPrice, $adabiyatPrice, 0, 0,
            $omoomiPack->includedInInstalments) : null;

//        dispatch(new GiveRaheAbrishamGifts($this->order));

        Cache::tags([
            'userAsset_'.$this->order->user_id,
            'user_'.$this->order->user_id.'_closedOrders',
        ])->flush();

    }
}
