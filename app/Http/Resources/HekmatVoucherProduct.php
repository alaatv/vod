<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Product
 *
 * @mixin \App\Product
 * */
class HekmatVoucherProduct extends AlaaJsonResource
{
    public function __construct(\App\Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Product)) {
            return [];
        }

        return [
            'name' => $this->name,
        ];
    }
}
