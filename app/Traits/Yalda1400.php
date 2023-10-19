<?php

namespace App\Traits;


use App\Classes\CacheFlush;
use App\Repositories\CouponRepo;
use App\Repositories\NetworkMarketingRepo;
use App\Repositories\OrderproductRepo;
use Illuminate\Support\Facades\Cache;
use Laravel\Octane\Facades\Octane;


trait Yalda1400
{
    public function calcYaldaChances(): ?int
    {
        $key = 'calcYaldaChances:user:'.$this->cacheKey();
        $tags = [CacheFlush::YALDA_1400_TAG.$this->id];

        return Cache::tags($tags)
            ->remember($key, config('constants.CACHE_60'), function () {
                $referralCodeScore = $this->usedReferralCode()->exists() ? 1 : 0;

                [
                    $abrishamBeforeScore,
                    $abrishamInScore,
                    $yaldaSubscriptions,
                ] = [
                    $this->countBeforeYaldaSingleAbrishams(),
                    $this->countInYaldaSingleAbrishams(),
                    $this->yaldaTotalCoupons() ?? 0,
                ];
                return max((1 + $referralCodeScore + $abrishamBeforeScore + $abrishamInScore) - $yaldaSubscriptions, 0);
            });
    }

    public function countBeforeYaldaSingleAbrishams(): ?int
    {
        $tags = [CacheFlush::YALDA_1400_TAG.$this->id];
        $key = 'countBeforeYaldaSingleAbrishams:user:'.$this->cacheKey();

        return Cache::tags($tags)
            ->remember($key, config('constants.CACHE_60'), function () {
                return OrderproductRepo::getPurchasedOrderproducts(Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS,
                    '2019-12-21 00:00:00',
                    config('constants.EVENTS.BEGIN'),
                    'all',
                    [config('constants.ORDER_PRODUCT_TYPE_DEFAULT'), config('constants.ORDER_PRODUCT_GIFT'),],
                    [$this->id])->count();
            });
    }

    public function countInYaldaSingleAbrishams(): ?int
    {
        $tags = [CacheFlush::YALDA_1400_TAG.$this->id];
        $key = 'countInYaldaSingleAbrishams:user:'.$this->cacheKey();

        return Cache::tags($tags)
            ->remember($key, config('constants.CACHE_60'), function () {
                return OrderproductRepo::getPurchasedOrderproducts(Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS,
                    config('constants.EVENTS.BEGIN'),
                    config('constants.EVENTS.END'),
                    'all',
                    [config('constants.ORDER_PRODUCT_TYPE_DEFAULT'), config('constants.ORDER_PRODUCT_GIFT'),],
                    [$this->id])->count();
            });
    }

    public function yaldaTotalCoupons()
    {
        $key = 'yaldaTotalSubscriptions:user:'.$this->cacheKey().':event:'.config('constants.EVENTS.YALDA_1400');
        $tags = [CacheFlush::YALDA_1400_TAG.$this->id];
        return Cache::tags($tags)
            ->remember($key, config('constants.CACHE_60'), function () {
                return CouponRepo::getCouponUserByCodePattern('c4y%', $this->id)->count();
            });
    }

    public function calculateYaldaLotteryPoints(?Order $order): ?int
    {
        if (!isset($order)) {
            return 0;
        }

        $tags = [CacheFlush::YALDA_1400_TAG.$this->id];
        $key = 'calculateYaldaLotteryPoints:order:'.$order->cacheKey();

        return Cache::tags($tags)
            ->remember($key,
                config('constants.CACHE_60'),
                function () use ($order) {
                    $totalPoints = 0;
                    $orderproducts = $order->orderproducts()->get();
                    foreach ($orderproducts as $orderproduct) {
                        $totalPoints += $this->getTotalPoints($orderproduct->product_id);
                    }
                    return $totalPoints;
                });
    }

    private function getTotalPoints($productId): int
    {
        return in_array($productId, Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS) ? 1 : match ($productId) {
            Product::RAHE_ABRISHAM99_PACK_TAJROBI => count(Product::ALL_ABRISHAM_PRODUCTS_EKHTESASI_TAJROBI),
            Product::RAHE_ABRISHAM99_PACK_RIYAZI => count(Product::ALL_ABRISHAM_PRODUCTS_EKHTESASI_RIYAZI),
            Product::RAHE_ABRISHAM1401_PACK_OMOOMI => count(Product::ALL_ABRISHAM_PRODUCTS_OMOOMI),
            default => 0,
        };
    }

    public function hasUsedReferralCodes(): ?bool
    {
        $usedRecords = NetworkMarketingRepo::getReferralCodeUserInstance($this->id,
            config('constants.EVENTS.YALDA_1400'))->first();
        return isset($usedRecords);
    }

    public function canGetPackScores(): ?bool
    {
        $tags = [CacheFlush::YALDA_1400_TAG.$this->id];
        $key = 'canGetPackScores:user:'.$this->cacheKey();

        return Cache::tags($tags)
            ->remember($key, config('constants.CACHE_60'), function () {
                [
                    $packCount,
                    $singleProducts
                ] = Octane::concurrently([
                    fn() => $this->countBeforeYaldaPackAbrishams(),
                    fn() => $this->countInYaldaSingleAbrishams(),
                ], config('constants.OCTANE_CONCURRENTLY_TIME_OUT'));
                return $packCount && !$this->isUserLotteryPointsBalance($packCount, $singleProducts);
            });
    }

    public function countBeforeYaldaPackAbrishams(): ?int
    {

        $tags = [CacheFlush::YALDA_1400_TAG.$this->id];
        $key = 'countBeforeYaldaPackAbrishams:user:'.$this->cacheKey();

        return Cache::tags($tags)
            ->remember($key, config('constants.CACHE_60'), function () {
                return OrderproductRepo::getPurchasedOrderproducts(Product::ALL_PACK_ABRISHAM_PRODUCTS,
                    '2019-12-21 00:00:00',
                    config('constants.EVENTS.BEGIN'),
                    'all',
                    [config('constants.ORDER_PRODUCT_TYPE_DEFAULT'), config('constants.ORDER_PRODUCT_GIFT'),],
                    [$this->id])->count();
            });
    }

    public function isUserLotteryPointsBalance(
        int $beforeYaldaPackAbrishamsCount,
        int $inYaldaSingleAbrishamsCount
    ): ?bool {
        $lotteryScores = $this->calcLotteryScores();
        return $lotteryScores >= $beforeYaldaPackAbrishamsCount + $inYaldaSingleAbrishamsCount;
    }

    public function calcLotteryScores(): ?int
    {
        return $this->userHasBon(config('constants.BON2'));
    }

}
