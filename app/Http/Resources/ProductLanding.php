<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductLanding extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = (array) $this->resource;
        return [
            'block' => $this->when(Arr::has($resource, 'block'),
                Arr::has($resource, 'block') ? BlockLanding::collection($this['block']) : null),
            'plan' => $this->when(Arr::has($resource, 'plan'), Arr::has($resource, 'plan') ? $resource['plan'] : null),
        ];
    }
}
