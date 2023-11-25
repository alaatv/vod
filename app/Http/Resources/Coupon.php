<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Coupon
 *
 * @mixin \App\Models\Coupon
 * */
class Coupon extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Models\Coupon)) {
            return [];
        }

        $this->loadMissing('coupontype', 'discounttype');

        return [
            'name' => $this->when(isset($this->name), $this->name),
            'code' => $this->when(isset($this->code), $this->code),
            'discount' => $this->discount,
            'coupontype' => $this->when(isset($this->coupontype_id), function () {
                return new Coupontype($this->coupontype);
            }),
            'discounttype' => $this->when(isset($this->discounttype_id), function () {
                return new Discounttype($this->discounttype);
            }),
        ];
    }
}
