<?php

namespace App\Http\Resources;

use App\Models\Event;

class EventResource extends AlaaJsonResource
{
    public function toArray($request)
    {
        if (!($this->resource instanceof Event)) {
            return [];
        }

        return [
            'id' => $this->id,
            'title' => $this->displayName,
            'name' => $this->name,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
        ];
    }
}
