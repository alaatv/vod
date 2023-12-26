<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class Userstatus
 *
 * @mixin \App\Models\Userstatus
 * */
class Userstatus extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (! ($this->resource instanceof \App\Models\Userstatus)) {
            return [];
        }

        return [
            'name' => $this->when(isset($this->name), $this->name),
        ];
    }
}
