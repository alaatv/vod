<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SlideShowInBlock extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'shortDescription' => $this->resource->shortDescription,
            'link' => $this->resource->link,
            'order' => $this->resource->order,
            'in_new_tab' => $this->resource->in_new_tab,
            'validSince' => $this->resource->validSince,
            'validUntil' => $this->resource->validUntil,
            'width' => $this->resource->width,
            'height' => $this->resource->height,
            'updated_at' => $this->resource->updated_at,
            'url' => $this->resource->url,
        ];
    }
}
