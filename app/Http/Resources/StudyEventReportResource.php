<?php

namespace App\Http\Resources;

use App\Models\StudyEventReport;
use Illuminate\Http\Request;

class StudyEventReportResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->report['message'],
            'date' => $this->report['date'],
        ];
    }

    public function resolve($request = null): array
    {
        if (!$this->resource instanceof StudyEventReport) {
            return [];
        }
        return parent::resolve($request);
    }
}
