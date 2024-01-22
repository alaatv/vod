<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class Attribute extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->info_attributes) && empty($this->extra_attributes)) {
            return null;
        }

        $resource = $this->resource;

        return [
            'info' => $this->when(!empty($resource->info_attributes),
                !empty($resource->info_attributes) ? new InfoAttribute($resource->info_attributes) : null),
            //          'extra' => $this->when(!empty($resource->extra_attributes), !empty($resource->extra_attributes) ? new ExtraAttribute($resource->extra_attributes) : null),
            'extra' => $this->when(!empty($resource->extra_attributes),
                !empty($resource->extra_attributes) ? $resource->extra_attributes : null),

            'subscription' => $this->when(!empty($resource->subscription_attributes),
                !empty($resource->subscription_attributes) ? $resource->subscription_attributes : null),

            //            'id' => $this->id,
            //            'name' => $this->when(isset($this->name), $this->name),
            //            'displayName' => $this->when(isset($this->displayName), $this->displayName),
            //            'description' => $this->when(isset($this->description), $this->description),
            //            'attributeControl' => new AttributeControl($this->attributecontrol),
            //            'attributeType' => new AttributeType($this->attributetype),
        ];
    }
}
