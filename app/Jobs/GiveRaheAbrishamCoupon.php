<?php

namespace App\Jobs;

use App\Models\Product;
use App\Notifications\ArashCouponNotification;
use App\Notifications\CouponGiftArashOmoomiPack;
use App\Notifications\CouponGiftArashPacks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GiveRaheAbrishamCoupon implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;

    private $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->queue = 'default2';
        $this->order = $order;
//        $this->arashProducts = Product::ARASH_PACK_PRODUCTS_ARRAY;
//        $this->takhasosiRiyaziArash = Product::ARASH_PACK_RITAZI_1400;
//        $this->takhasosiTajrobiArash = Product::ARASH_PACK_TAJROBI_1400;
//        $this->omoomiArash = Product::ARASH_PACK_OMOOMI_1400;
    }

    public function handle()
    {
        $orderOwner = $this->order->user;
        if (!isset($orderOwner)) {
            Log::channel('giveGiftProductErrors')->info('Owner of order '.$this->order->id.' is unknown. This order was skipped!');
            return null;
        }

        $orderproducts = $this->order->orderproducts;

        if (array_intersect($orderproducts->pluck('product_id')->toArray(), Product::ALL_PACK_ABRISHAM_PRODUCTS)) {
            $orderOwner->notify(new CouponGiftArashOmoomiPack());
        } elseif (array_intersect($orderproducts->pluck('product_id')->toArray(),
            Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS)) {
            $orderOwner->notify(new CouponGiftArashPacks());
        }
    }
}
