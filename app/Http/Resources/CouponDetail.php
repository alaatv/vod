<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CouponDetail extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'product_id' => $this->when(isset($this['productId']), $this['productId']),
            'product_name' => $this->when(isset($this['productName']), $this['productName']),
            'coupon_discount' => $this->when(isset($this['couponDiscount']), $this['couponDiscount']),
            'price_before_coupon' => $this->when(isset($this['priceBeforeCoupon']), $this['priceBeforeCoupon']),
            'price_after_coupon' => $this->when(isset($this['priceAfterCoupon']), $this['priceAfterCoupon']),
        ];
    }
}
