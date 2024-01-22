<?php

namespace App\Http\Resources\Soalaa;

use App\Http\Resources\AlaaJsonResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class SoalaaAttributeResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        if (empty($this->info_attributes) && empty($this->extra_attributes)) {
            return null;
        }

        $resource = $this->resource;

        return [
            'info' => $this->when(!empty($resource->info_attributes),
                new SoalaaInfoAttributeResource($resource->info_attributes)),
            'extra' => $this->when(!empty($resource->extra_attributes),
                !empty($resource->extra_attributes) ? $resource->extra_attributes : null),
        ];
    }
}
