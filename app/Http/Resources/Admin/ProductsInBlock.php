<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsInBlock extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'order' => $this->when($this->resource->pivot, $this->resource->pivot?->order),
            'name' => $this->when($this->resource->name, $this->resource->name),
        ];
    }
}
