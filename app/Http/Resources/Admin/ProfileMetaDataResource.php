<?php

namespace App\Http\Resources\Admin;


use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\Gender;
use App\Http\Resources\Grade;
use App\Http\Resources\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProfileMetaDataResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->resource;
        return [
            'provinces' => OstanResource::collection(Arr::get($resource, 'provinces')),
            'cities' => ShahrResource::collection(Arr::get($resource, 'cities')),
            'majors' => Major::collection(Arr::get($resource, 'majors')),
            'grades' => Grade::collection(Arr::get($resource, 'grades')),
            'genders' => Gender::collection(Arr::get($resource, 'genders')),
        ];
    }
}
