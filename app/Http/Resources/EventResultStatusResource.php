<?php

namespace App\Http\Resources;

use App\Models\Eventresultstatus;


class EventResultStatusResource extends AlaaJsonResource
{

    public function toArray($request)
    {
        if (!($this->resource instanceof Eventresultstatus)) {
            return [];
        }
        return [
            'id' => $this->id,
            'title' => $this->displayName,
        ];
    }
}
