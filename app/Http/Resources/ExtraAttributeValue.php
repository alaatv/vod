<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class User
 *
 * @mixin \App\Attributevalue
 * */
class ExtraAttributeValue extends AlaaJsonResource
{
    /**
     * Class User
     *
     * @mixin \App\User
     * */
    public function __construct(\App\Attributevalue $model)
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
        if (!($this->resource instanceof \App\Attributevalue)) {
            return [];
        }

        return [
            'title' => $this->name,
            'extra_cost' => $this->pivot->extraCost,
        ];
    }
}
