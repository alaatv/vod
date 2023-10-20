<?php

namespace App\Jobs;

use App\Models\Order;
use App\Repositories\ProductRepository;
use App\Services\OrderProductsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InterrelationProductsJob implements ShouldQueue
{
    private $order;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $interrelationProducts = ProductRepository::interrelationProducts();
        $interrelationProductsIds = $interrelationProducts->pluck('id');
        $interrelationProductsIds = $this->order->orderproducts->whereIn('product_id', $interrelationProductsIds);

        $interrelationProductsIds->each(function ($orderProductPack) use ($interrelationProducts) {
            $relatedProducts = $interrelationProducts->where('id', $orderProductPack->product_id)
                ->pluck('productProduct')->flatten();
            OrderProductsService::mapRelatedProductForAddToOrderProduct($relatedProducts, $orderProductPack,
                $this->order);
        });
    }
}
