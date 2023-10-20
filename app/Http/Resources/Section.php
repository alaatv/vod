<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\Section
 *
 * @mixin \App\Section
 * */
class Section extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Section)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name),
        ];
    }
}
