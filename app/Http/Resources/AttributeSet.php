<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class AttributeSet
 *
 * @mixin \App\Models\Attributeset
 */
class AttributeSet extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Models\Attributeset)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name),
            'description' => $this->when(isset($this->description), $this->description),
            'order' => $this->when(isset($this->order), $this->order),
        ];
    }
}
