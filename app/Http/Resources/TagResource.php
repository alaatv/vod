<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Tag)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'key' => $this->when(isset($this->key), $this->key),
            'enable' => $this->enable,
            'tagGroup' => (new TagResource($this->group))->response(),
            'description' => $this->when(isset($this->description), $this->description),
        ];
    }
}
