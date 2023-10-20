<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderproductRepo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GiveToorRaheAbrishamRiyaziProducts implements ShouldQueue
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

        $orderproducts->where('product_id',
            Product::RAHE_ABRISHAM_RIYAZI_PASS_AZMOON)->isEmpty() ? OrderproductRepo::createBasicOrderproduct($this->order->id,
            Product::RAHE_ABRISHAM_RIYAZI_PASS_AZMOON, 0, 0) : null;

        Cache::tags([
            'userAsset_'.$this->order->user_id,
            'user_'.$this->order->user_id.'_closedOrders',
        ])->flush();

    }
}
