<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class Bon
 *
 * @mixin \App\Models\Bon
 * */
class Bon extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Models\Bon)) {
            return [];
        }

        return [
            'name' => $this->name,
            'display_name' => $this->displayName,
        ];
    }
}
