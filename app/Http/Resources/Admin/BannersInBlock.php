<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BannersInBlock extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'order' => $this->resource->pivot?->order,
            'name' => $this->resource->title,
        ];
    }
}
