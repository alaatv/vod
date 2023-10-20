<?php

namespace App\Http\Resources;

use App\Models\TagGroup;
use Illuminate\Http\Request;


class TagGroupResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof TagGroup)) {
            return [];
        }

        $this->loadMissing('tags');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
//            'description' => $this->when(isset($this->description), $this->description),
            'tags' => TagLightResource::collection($this->tags),
        ];
    }
}
