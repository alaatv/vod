<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class ContentofplanTypeResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'title' => $this->title,
            'display_name' => $this->display_name,
        ];
    }
}
