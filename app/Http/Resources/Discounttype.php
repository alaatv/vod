<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class \App\Discounttype
 *
 * @mixin \App\Discounttype
 * */
class Discounttype extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Discounttype)) {
            return [];
        }

        return [
            'name' => $this->when(isset($this->name), $this->name),
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
        ];
    }
}
