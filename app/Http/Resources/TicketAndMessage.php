<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TicketAndMessage extends AlaaJsonResource
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
            'ticket' => new TicketWithoutMessage(Arr::get($resource, 'ticket')),
            'ticketMessage' => new TicketMessage(Arr::get($resource, 'ticketMessage')),
        ];
    }
}
