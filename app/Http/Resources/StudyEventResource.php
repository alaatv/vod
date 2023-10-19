<?php

namespace App\Http\Resources;

use App\Models\Studyevent;
use App\Models\Studyevent;
use Illuminate\Http\Request;


/**
 * Class StudyEventResource
 * @package App\Http\Resources
 * @mixin Studyevent
 */
class StudyEventResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Studyevent)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name),
            'title' => $this->when(isset($this->title), $this->title),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'major_id' => $this->major_id,
            'grade_id' => $this->grade_id,
            'method_id' => $this->method_id,
            'start_at' => $this->start_at,
        ];
    }
}

