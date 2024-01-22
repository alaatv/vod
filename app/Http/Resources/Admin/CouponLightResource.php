<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Coupon;
use Illuminate\Http\Request;

/**
 * Class CouponResource
 *
 * @mixin Coupon
 * */
class CouponLightResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Coupon)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name),
            'code' => $this->when(isset($this->code), $this->code),
            'discount' => $this->discount,
            'enable' => $this->enable,
        ];
    }
}
