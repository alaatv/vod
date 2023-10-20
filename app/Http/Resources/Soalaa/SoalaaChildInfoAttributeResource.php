<?php

namespace App\Http\Resources\Soalaa;

use App\Http\Resources\AlaaJsonResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class SoalaaChildInfoAttributeResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $resource = $this->resource;

        return [
            'examDate' => $this->when(!empty($resource['examDate']),
                !empty($resource['examDate']) ? $resource['examDate'] : null),
        ];
    }
}
