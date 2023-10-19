<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/5/2018
 * Time: 4:12 PM
 */

namespace App\Classes\Checkout\Alaa\Chains;

use App\Classes\Abstracts\Checkout\OrderproductCouponChecker;
use App\Models\Coupon;
use App\Traits\ProductCommon;
use Illuminate\Support\Collection;

class AlaaOrderproductCouponChecker extends OrderproductCouponChecker
{
    use ProductCommon;

    protected function IsIncludedInCoupon(Collection $orderproducts, Coupon $coupon): Collection
    {
        foreach ($orderproducts as $orderproduct) {
            $couponHasProduct = $coupon->hasProduct($orderproduct->product);

            if ($couponHasProduct) {
                $orderproduct->includeInCoupon();
            } else {
                $orderproduct->excludeFromCoupon();
            }
        }

        return $orderproducts;
    }
}
