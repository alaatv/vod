<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Employeeschedule;
use Illuminate\Http\Request;


/**
 * Class AttributeResource
 *
 * @mixin Employeeschedule
 */
class EmployeeScheduleResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        if (!($this->resource instanceof Employeeschedule)) {
            return [];
        }

        return [
            'id' => $this->id,
            'day' => $this->day,
            'beginTime' => explode(' ', $this->beginTime)[0],
            'finishTime' => explode(' ', $this->finishTime)[0],
            'lunchBreakInSeconds' => $this->lunchBreakInSeconds,
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
        ];
    }
}

