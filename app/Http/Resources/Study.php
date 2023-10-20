<?php

namespace App\Http\Resources;

use App\Http\Resources\Plan as PlanResource;
use App\Http\Resources\StudyPlan2 as StudyPlanResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Study extends AlaaJsonResource
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
            'days' => StudyplanResource::collection(Arr::get($resource, 'days')),
            'events' => PlanResource::collection(Arr::get($resource, 'events')),
        ];
    }
}
