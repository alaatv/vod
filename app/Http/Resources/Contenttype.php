<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\Contenttype
 *
 * @mixin \App\Contenttype
 * */
class Contenttype extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Contenttype)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'displayName' => $this->displayName,
        ];
    }
}
