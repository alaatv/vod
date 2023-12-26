<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class Orderstatus
 *
 * @mixin \App\Models\Orderstatus
 * */
class Orderstatus extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (! ($this->resource instanceof \App\Models\Orderstatus)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->displayName), $this->displayName),
        ];
    }
}
