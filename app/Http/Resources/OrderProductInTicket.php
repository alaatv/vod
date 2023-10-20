<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Product
 *
 * @mixin \App\Product
 * */
class OrderProductInTicket extends AlaaJsonResource
{
    public function __construct(\App\Orderproduct $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Orderproduct)) {
            return [];
        }

        return [
            'id' => $this->id,
            'price' => new Price($this->resource->price),
            'product' => new ProductInOrderproduct($this->product),
        ];
    }
}
