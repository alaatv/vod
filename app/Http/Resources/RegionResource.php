<?php

namespace App\Http\Resources;

use App\Models\Region;


class RegionResource extends AlaaJsonResource
{

    public function toArray($request)
    {
        if (!($this->resource instanceof Region)) {
            return [];
        }
        return [
            'id' => $this->id,
            'title' => $this->title
        ];
    }
}
