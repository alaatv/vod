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

class GiveAbrishamRiyaziProducts implements ShouldQueue
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
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $orderproducts = $this->order->orderproducts;

        $riziPack = $orderproducts->where('product_id', Product::RAHE_ABRISHAM99_PACK_RIYAZI)->first();

        if (!isset($riziPack)) {
            return null;
        }

        $products = ProductRepository::getProductsById([
            Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI, Product::RAHE_ABRISHAM99_SHIMI
        ])->get();

        $riaziPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI)->first()->price, 'base',
            0);
        $shimiPrice = Arr::get($products->where('id', Product::RAHE_ABRISHAM99_SHIMI)->first()->price, 'base', 0);

        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI, $riaziPrice, $riaziPrice, 0, 0,
            $riziPack->includedInInstalments) : null;
        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM99_SHIMI)->isEmpty() ? OrderproductRepo::createHiddenOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM99_SHIMI, $shimiPrice, $shimiPrice, 0, 0, $riziPack->includedInInstalments) : null;

//        dispatch(new GiveRaheAbrishamGifts($this->order));

        Cache::tags([
            'userAsset_'.$this->order->user_id,
            'user_'.$this->order->user_id.'_closedOrders',
        ])->flush();
    }
}
