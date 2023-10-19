<?php

namespace App\Http\Resources;

use App\Models\Shahr;
use App\Models\Shahr;
use Illuminate\Http\Request;


class ShahrInEntekhabReshte extends AlaaJsonResource
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
        if (!($this->resource instanceof Shahr)) {
            return [];
        }

        return [
            'id' => $this->id,
            'title' => $this->name,
            'order' => $this->pivot->order,
        ];
    }
}
