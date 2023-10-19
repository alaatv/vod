<?php

namespace App\Http\Resources;

use App\Models\LiveDescription;
use App\Models\LiveDescription;


class LiveDescriptionResource extends AlaaJsonResource
{

    public function toArray($request)
    {

        if (!($this->resource instanceof LiveDescription)) {
            return [];
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'seen_counter' => $this->seen_counter,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'has_pinned' => $this->pinned_at ? true : false,
            'photo' => $this->photo,
            'owner' => $this->owner,
            'entity_id' => $this->entity_id,
            'entity_type' => $this->entity_type,
        ];
    }
}
