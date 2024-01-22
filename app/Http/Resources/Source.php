<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class \App\Source
 *
 * @mixin \App\Source
 * */
class Source extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'photo' => $this->photo,
        ];
    }
}
