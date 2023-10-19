<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class LiveConductorResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'class_name' => $this->class_name,
            'title' => $this->title,
            'live_link' => $this->live_link,
            'description' => $this->description,
            'poster' => $this->poster,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'finish_time' => $this->finish_time,
        ];
    }
}
