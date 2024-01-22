<?php

namespace App\Http\Resources;

use App\Traits\Product\Resource;
use Illuminate\Support\Arr;

class InvoiceItem extends AlaaJsonResource
{
    use Resource;

    public function toArray($request)
    {
        $array = (array)$this->resource;
        //        return $array;
        $grand = Arr::get($array, 'grand');

        return [
            'grand' => $grand != null ? [
                'id' => $grand->id,
                'title' => $grand->name,
                'photo' => $grand->photo,
                'attributes' => new Attribute($grand),
            ] : null,
            'order_product' => OrderProduct::collection(Arr::get($array, 'orderproducts')),
        ];
    }
}
