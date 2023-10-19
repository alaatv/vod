<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Map
 *
 * @mixin \App\Map
 * */
class Map extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                'id' => $this->id,
                'title' => $this->title,
            ];
    }
}
