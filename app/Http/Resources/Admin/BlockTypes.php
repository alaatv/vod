<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BlockTypes extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'display_name' => $this->resource->display_name,
        ];
    }
}
