<?php

namespace App\Repositories;


use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CouponRepo
{
    public static function createBasicOveralCoupon(
        string $code,
        int $discount,
        string $name,
        $usageLimit = null,
        $description = null,
        $validSince = null,
        $validUntil = null,
        $hasPurchased = 0,
        $enable = 1
    ): ?Coupon {
        return Coupon::create([
            'code' => $code,
            'discount' => $discount,
            'coupontype_id' => config('constants.COUPON_TYPE_OVERALL'),
            'discounttype_id' => config('constants.DISCOUNT_TYPE_PERCENTAGE'),
            'name' => $name,
            'validSince' => $validSince,
            'validUntil' => $validUntil,
            'usageLimit' => $usageLimit,
            'description' => $description,
            'hasPurchased' => $hasPurchased,
            'enable' => $enable,
        ]);
    }

    public static function getRandomDiscount()
    {
        $discountPool = Cache::remember('discount_pool', config('constants.CACHE_600'), function () {
            $discountPool = collect();


            //discountCode = 40%    count=5000
            for ($i = 0; $i < 5; $i++) {
                $discountPool->push(40);
            }

            //discountCode = 45%    count=30000
            for ($i = 0; $i < 30; $i++) {
                $discountPool->push(45);
            }

            //discountCode = 50%    count=35000
            for ($i = 0; $i < 35; $i++) {
                $discountPool->push(50);
            }


            //discountCode = 55%    count=15000
            for ($i = 0; $i < 19; $i++) {
                $discountPool->push(55);
            }

            //discountCode = 60%    count=5000
            for ($i = 0; $i < 5; $i++) {
                $discountPool->push(60);
            }

            //discountCode = 70%    count=5000
            for ($i = 0; $i < 5; $i++) {
                $discountPool->push(70);
            }

            //discountCode = 100%    count=5000
            for ($i = 0; $i < 1; $i++) {
                $discountPool->push(100);
            }

            $discountPool = $discountPool->shuffle();
            return $discountPool;
        });
        return $discountPool->random();
    }

    public static function makeRandomPartialCoupon(
        ?string $prefix,
        int $discount,
        string $name,
        $usageLimit = null,
        $description = null,
        $validSince = null,
        $validUntil = null,
        $hasPurchased = 0,
        int $usageNumber = 0
    ): ?Coupon {
        do {
            $foundCode = $prefix.self::random_str(4);
            $foundCoupon = CouponRepo::findCouponByCode($foundCode);
        } while (isset($foundCoupon));

        return self::createBasicPartialCoupon($foundCode, $discount, $name, $usageLimit, $description, $validSince,
            $validUntil, $hasPurchased, $usageNumber);
    }

    private static function random_str(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyz'
    ): string {
        if ($length < 1) {
            throw new RangeException('Length must be a positive integer');
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    /**
     * @param  string  $code
     *
     * @return
     */
    public static function findCouponByCode(string $code): ?Coupon
    {
        return Coupon::where('code', $code)->first();
    }

    public static function createBasicPartialCoupon(
        string $code,
        int $discount,
        string $name,
        $usageLimit = null,
        $description = null,
        $validSince = null,
        $validUntil = null,
        $hasPurchased = 0,
        int $usageNumber = 0
    ): ?Coupon {
        return Coupon::create([
            'code' => $code,
            'discount' => $discount,
            'coupontype_id' => config('constants.COUPON_TYPE_PARTIAL'),
            'discounttype_id' => config('constants.DISCOUNT_TYPE_PERCENTAGE'),
            'name' => $name,
            'validSince' => $validSince,
            'validUntil' => $validUntil,
            'usageLimit' => $usageLimit,
            'description' => $description,
            'hasPurchased' => $hasPurchased,
            'usageNumber' => $usageNumber,
        ]);
    }

    public static function getCouponUserByCodePattern(string $pattern, int $userId): Builder|Coupon
    {
        return Coupon::where('code', 'like', $pattern)->whereHas('users', function ($q) use ($userId) {
            $q->where('id', $userId);
        });
    }

    public static function updateCouponWithRelations(Coupon $coupon)
    {
        return DB::transaction(function () use ($coupon) {

            if ($coupon->isDirty(['discount'])) {
                return $coupon->update() &&
                    $coupon->orders()
                        ->where('orderstatus_id', config('constants.ORDER_STATUS_OPEN'))
                        ->update(['couponDiscount' => $coupon->discount]);
            }

            return $coupon->update();
        });
    }
}
