<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SetsInBlock extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'order' => $this->when($this->resource->pivot, $this->resource->pivot?->order),
        ];
    }
}
