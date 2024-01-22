<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class \App\Slideshow
 *
 * @mixin \App\Slideshow
 * */
class Slideshow extends AlaaJsonResource
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
            'title' => $this->when(isset($this->title), $this->title),
            'body' => $this->when(isset($this->shortDescription), $this->shortDescription),
            'photo' => $this->when(isset($this->url), $this->url),
            'link' => $this->when(isset($this->link), $this->link),
            'order' => $this->when(isset($this->order), $this->order),
        ];
    }
}
