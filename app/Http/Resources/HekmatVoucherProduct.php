<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Product
 *
 * @mixin \App\Models\Product
 * */
class HekmatVoucherProduct extends AlaaJsonResource
{
    public function __construct(\App\Models\Product $model)
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
        if (!($this->resource instanceof \App\Models\Product)) {
            return [];
        }

        return [
            'name' => $this->name,
        ];
    }
}
