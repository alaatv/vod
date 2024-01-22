<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Invoice extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $array = (array)$this->resource;
        $items = Arr::get($array, 'items');

        return [
            'items' => $this->when(isset($items), count($items) > 0 ? InvoiceItem::collection($items) : null),
            'count' => $this->when(Arr::has($array, 'orderproductCount'),
                Arr::has($array, 'orderproductCount') ? Arr::get($array, 'orderproductCount') : 0),
            'price' => $this->when(Arr::has($array, 'price'),
                Arr::has($array, 'price') ? new Price(Arr::get($array, 'price')) : null),
            'pay_by_wallet' => $this->when(Arr::has($array, 'payByWallet'),
                Arr::has($array, 'payByWallet') ? Arr::get($array, 'payByWallet') : null),
            'coupon' => $this->when(Arr::has($array, 'coupon'), new CouponInfo(Arr::get($array, 'coupon'))),
            'referralCode' => $this->when(Arr::has($array, 'referralCode'),
                new ReferralCodeInfo(Arr::get($array, 'referralCode'))),
            'order_has_donate' => $this->when(Arr::has($array, 'orderHasDonate'), Arr::get($array, 'orderHasDonate')),
            'redirect_to_gateway' => $this->when(Arr::has($array, 'redirectToGateway'),
                Arr::has($array, 'redirectToGateway') ? Arr::get($array, 'redirectToGateway') : null),
        ];
    }
}
