<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Notifications\GiftGiven4;
use App\Repositories\OrderproductRepo;
use App\Repositories\ProductRepository;
use App\Traits\ProductCommon;
use App\Traits\User\AssetTrait;
use App\Traits\UserCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GiveRaheAbrishamGifts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use ProductCommon;
    use UserCommon;
    use AssetTrait;

    public $queue;
    private $godarProducts;
    private $arashProducts;
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
        $this->godarProducts = ProductRepository::getProductsById(Product::GODAR_ALL)->get();
        $this->arashProducts = ProductRepository::getProductsById(Product::ARASH_PRODUCTS_ARRAY)->get();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orderOwner = $this->order->user;
        if (!isset($orderOwner)) {
            Log::channel('giveGiftProductErrors')->info('Owner of order '.$this->order->id.' is unknown. This order was skipped!');
            return null;
        }

        $orderproducts = $this->order->orderproducts;
        foreach ($orderproducts as $orderproduct) {
            $giftProducts = $this->getGiftsOfRaheAbrisham($orderproduct->product);
            foreach ($giftProducts as $giftProduct) {
                $price = $giftProduct->price;
                if ($orderproducts->where('product_id', $giftProduct->id)->where('orderproducttype_id',
                    config('constants.ORDER_PRODUCT_GIFT'))->isNotEmpty()) {
                    continue;
                }

                try {
                    OrderproductRepo::createGiftOrderproduct($this->order->id, $giftProduct->id, $price['base']);
                } catch (QueryException $e) {
                    Log::channel('giveGiftProductErrors')->info('Database error on inserting product '.$giftProduct->id.' to order '.$this->order->id);
                    continue;
                }

            }
        }

        $orderOwner->notify(new GiftGiven4('راه ابریشمی', 'آرش'));

        Cache::tags(['user_'.$orderOwner->id.'_closedOrders', 'userAsset_'.$orderOwner->id,])->flush();
        Cache::tags(['order_'.$this->order->id])->flush();
        return null;
    }

    private function getGodarProduct(int $productId): ?Product
    {
        return optional($this->godarProducts->where('id', $productId))->first();
    }

    private function getArashProduct(int $productId): ?Product
    {
        return optional($this->arashProducts->where('id', $productId))->first();
    }
}
