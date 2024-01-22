<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ContentInComeGroupIndexResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->resource;

        return [
            'content_id' => Arr::get($resource, 'content_id'),
            'sum' => Arr::get($resource, 'sum'),
            'count' => Arr::get($resource, 'count'),
            'index_link' => route('contentIncome.index', ['content_id' => Arr::get($resource, 'content_id')]),
        ];
    }
}
