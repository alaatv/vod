<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class InvoiceWithOnlyPrice extends AlaaJsonResource
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
        $array = (array) $this->resource;

        return [
            'price' => $this->when(Arr::has($array, 'price'),
                Arr::has($array, 'price') ? new Price(Arr::get($array, 'price')) : null),
            'pay_by_wallet' => $this->when(Arr::has($array, 'payByWallet'),
                Arr::has($array, 'payByWallet') ? Arr::get($array, 'payByWallet') : null),
        ];
    }
}
