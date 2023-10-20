<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ContentInBLock extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'order' => $this->resource->order,
            'name' => $this->resource->name,
        ];
    }
}
