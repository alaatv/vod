<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Attributevalue
 *
 * @mixin \App\Models\Attributevalue
 * */
class Attributevalue extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Models\Attributevalue)) {
            return [];
        }

        return [
            'attribute_id' => $this->attribute_id,
            'name' => $this->name,
            'extra_cost' => optional($this->pivot)->extraCost,
        ];
    }
}
