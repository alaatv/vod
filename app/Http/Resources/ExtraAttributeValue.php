<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class User
 *
 * @mixin \App\Models\Attributevalue
 * */
class ExtraAttributeValue extends AlaaJsonResource
{
    /**
     * Class User
     *
     * @mixin \App\Models\User
     * */
    public function __construct(\App\Models\Attributevalue $model)
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
        if (! ($this->resource instanceof \App\Models\Attributevalue)) {
            return [];
        }

        return [
            'title' => $this->name,
            'extra_cost' => $this->pivot->extraCost,
        ];
    }
}
