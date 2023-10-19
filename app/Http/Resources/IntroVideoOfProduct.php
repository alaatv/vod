<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class IntroVideoOfProduct extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->resource;
        return [
            'video' => $this->when(isset($resource->intro_video), function () use ($resource) {
                return $resource->intro_video ?? null;
            }),
            'photo' => $this->when(isset($resource->intro_video_thumbnail), function () use ($resource) {
                return $resource->intro_video_thumbnail ?? null;
            }),
        ];
    }
}
