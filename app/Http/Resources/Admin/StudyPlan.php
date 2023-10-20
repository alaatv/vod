<?php

namespace App\Http\Resources\Admin;


use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\Content;
use App\Http\Resources\Plan;
use App\Http\Resources\StudyEventResource;
use Illuminate\Http\Request;


/**
 * Class StudyPlanResource
 * @package App\Http\Resources
 * @mixin \App\Studyplan
 */
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
        if (!($this->resource instanceof \App\Studyplan)) {
            return [];
        }

        return [
            'id' => $this->id,
            'event' => $this->when(isset($this->event_id), function () {
                return new StudyEventResource($this->event);
            }),
            'row' => $this->when(isset($this->row), $this->row),
            'voice' => $this->when(isset($this->voice), $this->voice),
            'body' => $this->when(isset($this->body), $this->body),
            'title' => $this->when(isset($this->title), $this->title),
            'plan_date' => $this->when(isset($this->plan_date), $this->plan_date),
            'plans' => $this->when(isset($this->plans), function () {
                return Plan::collection($this->plans);
            }),
            'contents' => $this->when(isset($this->contents), function () {
                return Content::collection($this->contents);
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
        ];
    }
}

