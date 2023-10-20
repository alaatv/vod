<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class DurationAttribute extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->info_attributes) && empty($this->extra_attributes)) {
            return null;
        }

        $resource = $this->resource;

        return [
            'info' => $this->when(!empty($resource->duration_attribute),
                !empty($resource->duration_attribute) ? new InfoAttribute($resource->duration_attribute) : null),
//          'extra' => $this->when(!empty($resource->extra_attributes), !empty($resource->extra_attributes) ? new ExtraAttribute($resource->extra_attributes) : null),
            'extra' => $this->when(!empty($resource->extra_attributes),
                !empty($resource->extra_attributes) ? $resource->extra_attributes : null),
        ];
    }
}
