<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class \App\Models\Studyplan
 *
 * @mixin \App\Models\Studyplan
 * */
class StudyPlan extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource;

        return [
            'studyPlan_id' => $data->id,
            'id' => $data->plan_date,
            'date' => $data->plan_date,
            'voice' => $data->voice,
            'body' => $data->body,
            'title' => $data->title,
            'contents' => null,
            'is_current' => now('Asia/Tehran')->format('Y-m-d') == $data->plan_date,
        ];
    }
}
