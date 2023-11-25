<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Producttype
 *
 * @mixin \App\Models\Producttype
 * */
class Producttype extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Models\Producttype)) {
            return [];
        }

        return [
            'type' => $this->id,
        ];
    }
}
