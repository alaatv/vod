<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class AttributeControl
 *
 * @mixin \App\Attributecontrol
 */
class AttributeControl extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Attributecontrol)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name),
            'description' => $this->when(isset($this->description), $this->description),
        ];
    }
}
