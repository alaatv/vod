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
use Illuminate\Support\Facades\Cache;

class GiveArash1401SubProducts implements ShouldQueue
{
    use Dispatchable;
    use GiveGiftsHelper;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const PRODUCT_MAP =
        [
            Product::ARASH_PACK_TAJROBI_1401 => [
                Product::ARASH_ZIST_1401,
                Product::ARASH_SHIMI_1401,
                Product::ARASH_RIYAZI_TAJROBI_1401,
            ],
            Product::ARASH_PACK_RIYAZI_1401 => [
                Product::ARASH_RIYAZI_RIYAZI_1401,
                Product::ARASH_SHIMI_1401,
            ],
            Product::ARASH_PACK_OMOOMI_1401 => [
                Product::ARASH_ZABAN_1401,
                Product::ARASH_ADABIYAT_1401,
                Product::ARASH_DINI_1401,
                Product::ARASH_ARABI_1401
            ],
            Product::TITAN_PACK_OMOOMI_1401 => [
                Product::TITAN_DINI_1401,
                Product::TITAN_ADABIYAT_1401,
                Product::TITAN_ZABAN_1401,
                Product::TITAN_ARABI_1401,
            ],
            Product::TITAN_PACK_TAJROBI_1401 => [
                Product::TITAN_SHIMI_1400,
                Product::TITAN_RIYAZI_TAJROBI_1401,
                Product::TITAN_ZIST_1401,
                Product::TITAN_FIZIK_1400,
            ],
            Product::TITAN_PACK_RIYAZI_1401 => [
                Product::TITAN_HESABAN_1401,
                Product::TITAN_HENDESE_1400,
                Product::TITAN_AMAR_1400,
                Product::TITAN_SHIMI_1400,
                Product::TITAN_FIZIK_1400,
            ],
            Product::ARASH_TITAN_RIYAZI_TAJORBI => [
                Product::ARASH_RIYAZI_TAJROBI_1401,
                Product::TITAN_RIYAZI_TAJROBI_1401,
            ],
            Product::ARASH_TITAN_ZIST => [
                Product::ARASH_ZIST_1401,
                Product::TITAN_ZIST_1401,
            ],
            Product::ARASH_TITAN_RIYAZI_RIYAZI => [
                Product::ARASH_RIYAZI_RIYAZI_1401,
                Product::TITAN_HESABAN_1401,
                Product::TITAN_HENDESE_1400,
                Product::TITAN_AMAR_1400,
            ],
            Product::ARASH_TITAN_SHIMI => [
                Product::ARASH_SHIMI_1401,
                Product::TITAN_SHIMI_1400,
            ],
            Product::ARASH_TITAN_FIZIK => [
                Product::TITAN_FIZIK_1400,
            ],
            Product::ARASH_TITAN_ARABI => [
                Product::ARASH_ARABI_1401,
                Product::TITAN_ARABI_1401,
            ],
            Product::ARASH_TITAN_ZABAN => [
                Product::ARASH_ZABAN_1401,
                Product::TITAN_ZABAN_1401,
            ],
            Product::ARASH_TITAN_ADABIYAT => [
                Product::ARASH_ADABIYAT_1401,
                Product::TITAN_ADABIYAT_1401,
            ],
            Product::ARASH_TITAN_DINI => [
                Product::ARASH_DINI_1401,
                Product::TITAN_DINI_1401
            ],
            Product::ARASH_TITAN_PACK_TAJROBI => [
                Product::ARASH_ZIST_1401,
                Product::ARASH_SHIMI_1401,
                Product::ARASH_RIYAZI_TAJROBI_1401,
                Product::TITAN_SHIMI_1400,
                Product::TITAN_RIYAZI_TAJROBI_1401,
                Product::TITAN_ZIST_1401,
                Product::TITAN_FIZIK_1400,
            ],
            Product::ARASH_TITAN_PACK_RIYAZI => [
                Product::ARASH_RIYAZI_RIYAZI_1401,
                Product::ARASH_SHIMI_1401,
                Product::TITAN_HESABAN_1401,
                Product::TITAN_HENDESE_1400,
                Product::TITAN_AMAR_1400,
                Product::TITAN_SHIMI_1400,
                Product::TITAN_FIZIK_1400,
            ],
            Product::ARASH_TITAN_PACK_OMOOMI => [
                Product::ARASH_ZABAN_1401,
                Product::ARASH_ADABIYAT_1401,
                Product::ARASH_DINI_1401,
                Product::ARASH_ARABI_1401,
                Product::TITAN_DINI_1401,
                Product::TITAN_ADABIYAT_1401,
                Product::TITAN_ZABAN_1401,
                Product::TITAN_ARABI_1401,
            ],
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

        Cache::tags([
            'userAsset_'.$this->order->user_id,
            'user_'.$this->order->user_id.'_closedOrders',
        ])->flush();

    }
}
