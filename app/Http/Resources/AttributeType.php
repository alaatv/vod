<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class AttributeType
 *
 * @mixin \App\Models\Attributetype
 */
class AttributeType extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Models\Attributetype)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name),
            'description' => $this->when(isset($this->description), $this->description),
        ];
    }
}
