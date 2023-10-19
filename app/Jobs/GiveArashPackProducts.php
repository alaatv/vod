<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderproductRepo;
use App\Traits\User\AssetTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GiveArashPackProducts implements ShouldQueue
{
    use AssetTrait;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;
    private $order;

    /**
     * Create a new job instance.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        $this->queue = 'default2';
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orderproducts = $this->order->orderproducts;
        $orderProductIds = $orderproducts->pluck('product_id')->toArray();

        if (in_array(Product::ARASH_PACK_OMOOMI_1400, $orderProductIds)) {
            $this->giveProducts(Product::ARASH_PACK_OMOOMI_1400_SUBSET, $orderProductIds);
        }

        if (in_array(Product::ARASH_PACK_RITAZI_1400, $orderProductIds)) {
            $this->giveProducts(Product::ARASH_PACK_RIYAZI_1400_SUBSET, $orderProductIds);
        }

        if (in_array(Product::ARASH_PACK_TAJROBI_1400, $orderProductIds)) {
            $this->giveProducts(Product::ARASH_PACK_TAJROBI_1400_SUBSET, $orderProductIds);
        }

        if (in_array(Product::ARASH_PACK_TAJROBI, $orderProductIds)) {
            $this->giveProducts(Product::ARASH_PACK_TAJROBI_99_SUBSET, $orderProductIds);
        }

        if (in_array(Product::ARASH_PACK_RIYAZI, $orderProductIds)) {
            $this->giveProducts(Product::ARASH_PACK_RIYAZI_99_SUBSET, $orderProductIds);
        }

        Cache::tags(['user_'.$this->order->user_id.'_closedOrders', 'userAsset_'.$this->order->user_id,])->flush();
        Cache::tags(['order_'.$this->order->id])->flush();

    }

    public function giveProducts($productIds, $product_ids)
    {
        foreach ($productIds as $productId) {
            if (in_array($productId, $product_ids)) {
                continue;
            }

            $product = Product::find($productId);
            if (!isset($product)) {
                continue;
            }

            OrderproductRepo::createBasicOrderproduct($this->order->id, $productId, 0, 0);
        }
    }
}
