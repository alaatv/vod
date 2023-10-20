<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class ProductSamplePhoto extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->when(isset($this->title) && strlen($this->title) > 0, $this->title),
            'photo' => $this->when(isset($this->url) && strlen($this->url) > 0, $this->url),
        ];
    }
}
