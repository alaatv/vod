<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class CouponInfoWithPrice extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->resource;
        return [
            'price' => $this->when(isset($resource['priceInfo']),
                isset($resource['priceInfo']) ? new Price($resource['priceInfo']) : null),
            'pay_by_wallet' => $this->when(isset($resource['payableByWallet']),
                isset($resource['payableByWallet']) ? $resource['payableByWallet'] : null),
            'coupon' => new CouponInfo($resource['coupon']),
        ];
    }
}
