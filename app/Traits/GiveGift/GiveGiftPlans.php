<?php


namespace App\Traits\GiveGift;


use App\Classes\CacheFlush;
use App\Models\Major;
use App\Models\Product;
use App\Models\User;
use App\Notifications\Give4kGifts;
use App\Notifications\Give4kGiftsLink;
use App\Repositories\OrderRepo;
use Illuminate\Support\Facades\Log;

trait GiveGiftPlans
{
    use GiveGiftsHelper;

    // first detect user major base products in $order
    public function azmoon(User $user, $action)
    {
//        Log::channel('debug')->info("$action user: ".$user->id);
        $riaziGifts = self::PLANS[$action][self::GIFTS][self::REYAZI][0];
        $tajrobiGifts = self::PLANS[$action][self::GIFTS][self::TAJROBI][0];
        $ensaniGifts = self::PLANS[$action][self::GIFTS][self::ENSANI][0];
        $riaziSpecific = Product::ARASH_RIYAZI_SPECIFIC;
        $tajrobiSpecific = Product::ARASH_TAJROBI_SPECIFIC;

        $userOrderProducts = collect();
        $userArashOrderProductsPurchased = collect();
        $userArashOrders = OrderRepo::generalOrderSelectionWithUser(Product::ARASH_PRODUCTS_ARRAY, [$user->id]);

        foreach ($userArashOrders->get() as $order) {
            $userArashOrderProductsPurchased->push([$order->orderproducts->pluck('product_id')]);
        }

        Log::error("$action user: checking arash purchase");
        if ($userArashOrderProductsPurchased->isEmpty()) {
            return null;
        }

        $userArashOrderProductsPurchased = $userArashOrderProductsPurchased->flatten();

        // collect all user orderProducts
        $userOrders = OrderRepo::generalOrderSelectionWithUser(Product::ARASH_PRODUCTS_ARRAY, [$user->id])->get();
        foreach ($userOrders as $order) {
            $userOrderProducts->push([$order->orderproducts->pluck('product_id')]);
        }
        $userOrderProducts = $userOrderProducts->flatten();

        if ($userOrderProducts->contains($riaziGifts) || $userOrderProducts->contains($tajrobiGifts) || $userOrderProducts->contains($ensaniGifts)) {
            return null;
        }

        // set gift flags
        $riaziFlag = array_intersect($userArashOrderProductsPurchased->toArray(), $riaziSpecific);
        $tajrobiFlag = array_intersect($userArashOrderProductsPurchased->toArray(), $tajrobiSpecific);

        Log::error("$action user: ".$user->id.' riyaziFlag: '.empty($riaziFlag).' tajrobiFlag: '.empty($tajrobiFlag));
        // collect all gifts
        $gifts = collect();
        if ($riaziFlag) {
            $gifts->push($riaziGifts);
        }
        if ($tajrobiFlag) {
            $gifts->push($tajrobiGifts);
        }

        if ($user->major_id == Major::RIYAZI && $gifts->isEmpty()) {
            $gifts->push($riaziGifts);
        } elseif ($user->major_id == Major::TAJROBI && $gifts->isEmpty()) {
            $gifts->push($tajrobiGifts);
        } elseif ($user->major_id == Major::ENSANI && $gifts->isEmpty()) {
            $gifts->push($ensaniGifts);
        }

        if ($gifts->isEmpty()) {
            return null;
        }

        Log::error("$action user: ".$user->id.' : start giving gifts');

        // give gifts
        $giftGiven = false;

        $mainQuery = clone $userArashOrders;
        if ($gifts->contains($riaziGifts)) {
            $temp = clone $userArashOrders;
            $order = $userArashOrders->whereHas('orderproducts', function ($q) use ($riaziSpecific) {
                $q->whereIn('product_id', $riaziSpecific);
            })->first();
            if (!$order) {
                $userArashOrders = clone $temp;
                $order = $userArashOrders->whereHas('orderproducts', function ($q) {
                    $q->whereIn('product_id', Product::ARASH_PRODUCTS_ARRAY);
                })->first();
            }
            $giftGiven = $this->giveGiftProducts($order, [$riaziGifts]);
        }

        $userArashOrders = clone $mainQuery;
        if ($gifts->contains($tajrobiGifts)) {
            $temp = clone $userArashOrders;
            $order = $userArashOrders->whereHas('orderproducts', function ($q) use ($tajrobiSpecific) {
                $q->whereIn('product_id', $tajrobiSpecific);
            })->first();
            if (!$order) {
                $userArashOrders = clone $temp;
                $order = $userArashOrders->whereHas('orderproducts', function ($q) {
                    $q->whereIn('product_id', Product::ARASH_PRODUCTS_ARRAY);
                })->first();
            }
            $giftGiven = $this->giveGiftProducts($order, [$tajrobiGifts]);
        }

        $userArashOrders = clone $mainQuery;
        if ($gifts->contains($ensaniGifts)) {
            $order = $userArashOrders->whereHas('orderproducts', function ($q) {
                $q->whereIn('product_id', Product::ARASH_PRODUCTS_ARRAY);
            })->first();
            $giftGiven = $this->giveGiftProducts($order, [$ensaniGifts]);
        }
        Log::error("$action user: ".$user->id.' : gift given : '.$giftGiven.' to order '.$order->id);
        if ($giftGiven) {
            $order->user->notify(new Give4kGifts('آلایی', 'آرش', '4K', 'آرش', 'http://alaa.tv/6PEXt'));
        } else {
            $order->user->notify(new Give4kGiftsLink('آلایی', 'آرش', '4K', 'آرشی', 'http://alaa.tv/795Rq'));
        }

        CacheFlush::flushAssetCache($user);

    }

    public function arash($order, $action)
    {
        $giftables = self::PLANS[$action][self::PRODUCTS];
        $orderProductIds = $order->orderproducts->pluck('product_id')->toArray();

        foreach ($giftables as $giftableKey => $giftableSubset) {
            if (in_array($giftableKey, $orderProductIds)) {
                $flag = $this->giveGiftProducts($order, $giftableSubset);
                if ($flag) {
                    // todo: send notification
                }
            }
        }

    }

    public function titan_adabiyat($order, $action)
    {
        $flag = $this->giveGiftProducts($order, [Product::TITAN_ADABIYAT_1400]);
        if ($flag) {
            // todo: send notification
        }
    }

}
