<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\Coupontype;
use App\Http\Resources\Discounttype;
use App\Models\Coupon;
use Illuminate\Http\Request;

/**
 * Class CouponResource
 *
 * @mixin Coupon
 * */
class CouponResource extends AlaaJsonResource
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

        $this->loadMissing('coupontype', 'discounttype', 'products');

        return [
            'id' => $this->id,
            'coupontype' => $this->when(isset($this->coupontype_id), function () {
                return new Coupontype($this->coupontype);
            }),
            'hasPurchased' => $this->hasPurchased,
            'discounttype' => $this->when(isset($this->discounttype_id), function () {
                return new Discounttype($this->discounttype);
            }),
            'name' => $this->when(isset($this->name), $this->name),
            'enable' => $this->enable,
            'description' => $this->when(isset($this->description), $this->description),
            'code' => $this->when(isset($this->code), $this->code),
            'discount' => $this->discount,
            'maxCost' => $this->when(isset($this->maxCost), $this->maxCost),
            'usageLimit' => $this->when(isset($this->usageLimit), $this->usageLimit),
            'usageNumber' => $this->usageNumber,
            'required_products' => $this->when(isset($this->required_products), $this->required_products),
            'unrequired_products' => $this->when(isset($this->unrequired_products), $this->unrequired_products),
            'validSince' => $this->when(isset($this->validSince), $this->validSince),
            'validUntil' => $this->when(isset($this->validUntil), $this->validUntil),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'products' => ProductLightResource::collection($this->products),
            'is_strict' => $this->is_strict,
        ];
    }
}
