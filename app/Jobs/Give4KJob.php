<?php

namespace App\Jobs;

use App\Models\Major;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\Give4kGifts;
use App\Notifications\Give4kGiftsLink;
use App\Traits\GiveGift\GiveGiftsHelper;
use App\Traits\User\AssetTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Give4KJob implements ShouldQueue
{
    use AssetTrait;
    use Dispatchable;
    use GiveGiftsHelper;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $order;
    protected $notificationsBeSent;

    public function __construct(Order $order, bool $notificationsBeSent = true)
    {
        $this->order = $order;
        $this->notificationsBeSent = $notificationsBeSent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->order->user;
        $userMajor = $user->major_id;
        $gifts = collect();
        $orderProducts = $this->order->orderproducts->pluck('product_id')->toArray();
        $isArashy = array_intersect($orderProducts, Product::ARASH_PRODUCTS_ARRAY);
        if (!$isArashy) {
            return null;
        }

        $has4kRiyazi = $this->searchProductTreeInUserAssetsCollection(Product::find(Product::RIAZI_4K), $user);
        $has4kTajrobi = $this->searchProductTreeInUserAssetsCollection(Product::find(Product::TAJROBI_4K), $user);
        $has4kEnsani = $this->searchProductTreeInUserAssetsCollection(Product::find(Product::ENSANI_4K), $user);


        if (array_intersect($orderProducts, Product::ARASH_RIYAZI_SPECIFIC) && !$has4kRiyazi) {
            $gifts->push(Product::RIAZI_4K);
        }

        if (array_intersect($orderProducts, Product::ARASH_TAJROBI_SPECIFIC) && !$has4kTajrobi) {
            $gifts->push(Product::TAJROBI_4K);
        }

        if ($gifts->isEmpty() && $userMajor == Major::RIYAZI && !$has4kRiyazi) {
            $gifts->push(Product::RIAZI_4K);
        }

        if ($gifts->isEmpty() && $userMajor == Major::TAJROBI && !$has4kTajrobi) {
            $gifts->push(Product::TAJROBI_4K);
        }

        if ($gifts->isEmpty() && $userMajor == Major::ENSANI && !$has4kEnsani) {
            $gifts->push(Product::ENSANI_4K);
        }

        $giftGiven = false;
        foreach ($gifts as $gift) {
            $giftGiven = $this->giveGiftProducts($this->order, [$gift]);
        }

        if (!$this->notificationsBeSent) {
            return null;
        }

        if ($giftGiven) {
            $user->notify(new Give4kGifts('آلایی', 'آرش', '4K', 'آرش', 'http://alaa.tv/6PEXt'));
        } else {
            $user->notify(new Give4kGiftsLink('آلایی', 'آرش', '4K', 'آرشی', 'http://alaa.tv/795Rq'));
        }

    }
}
