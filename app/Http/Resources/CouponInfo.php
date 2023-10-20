<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class CouponInfo extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->when(isset($this->couponName), $this->couponName),
            'code' => $this->when(isset($this->couponCode), $this->couponCode),
            'discount' => $this->when(isset($this->totalDiscount), $this->totalDiscount),
            'detail' => $this->when(isset($this->detail), function () {
                return isset($this->detail) ? CouponDetail::collection(collect($this->detail)) : null;
            }),
        ];
    }
}
