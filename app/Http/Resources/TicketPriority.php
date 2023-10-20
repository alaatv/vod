<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\TicketPriority
 *
 * @mixin \App\TicketPriority
 * */
class TicketPriority extends AlaaJsonResource
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
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
