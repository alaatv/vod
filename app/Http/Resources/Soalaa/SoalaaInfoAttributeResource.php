<?php

namespace App\Http\Resources\Soalaa;

use App\Http\Resources\AlaaJsonResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class SoalaaInfoAttributeResource extends AlaaJsonResource
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
            'sal' => $this->when(!empty($resource['sal']), $resource['sal']),
            'grade' => $this->when(!empty($resource['grade']), $resource['grade']),
            'major' => $this->when(!empty($resource['major']), $resource['major']),
        ];
    }
}
