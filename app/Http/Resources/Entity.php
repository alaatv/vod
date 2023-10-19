<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class Entity extends AlaaJsonResource
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
        return [
            'entity_id' => $this->entity_id,
            'entity_type' => $this->entity_type,
        ];
    }
}
