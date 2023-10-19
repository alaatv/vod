<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Traits\GiveGift\GiveGiftsHelper;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GiveAbrishamProProducts implements ShouldQueue
{
    use Dispatchable;
    use GiveGiftsHelper;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const PRODUCT_MAP = [
        Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI => [
            Product::RAHE_ABRISHAM1401_PRO_SHIMI,
            Product::RAHE_ABRISHAM1401_PRO_ZIST,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI,
        ],
        Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI => [
            Product::RAHE_ABRISHAM1401_PRO_SHIMI,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI,
        ],
        Product::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI => Product::ALL_ABRISHAM_PRO_PRODUCTS_OMOOMI,
    ];
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
        $this->giveHiddenSubProduct($this->order, self::PRODUCT_MAP);
    }
}
