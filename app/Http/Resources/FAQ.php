<?php

namespace App\Http\Resources;

use App\Models\Websitesetting;
use Illuminate\Http\Request;


class FAQ extends AlaaJsonResource
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
            'title' => $this->resource->title,
            'body' => $this->resource->body,
            'photo' => Websitesetting::getFaqPhoto($this->resource),
            'video' => $this->resource->video,
            'order' => $this->resource->order,
        ];
    }
}
