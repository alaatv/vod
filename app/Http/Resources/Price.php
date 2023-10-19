<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Price extends AlaaJsonResource
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
            'base' => $this->when(Arr::has($array, 'base'), Arr::get($array, 'base')),
            'discount' => $this->when(Arr::has($array, 'discount'), Arr::get($array, 'discount')),
            'final' => $this->when(Arr::has($array, 'final'), Arr::get($array, 'final')),
            'payableByWallet' => $this->when(Arr::has($array, 'payableByWallet'), Arr::get($array, 'payableByWallet')),
            'final_instalmentally' => $this->when(Arr::has($array, 'final_instalmentally'),
                Arr::get($array, 'final_instalmentally')),
            'discount_instalmentally' => $this->when(Arr::has($array, 'discount_instalmentally'),
                Arr::get($array, 'discount_instalmentally')),
        ];
    }
}
