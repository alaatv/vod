<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\TicketDepartment
 *
 * @mixin \App\TicketDepartment
 * */
class TicketDepartment extends AlaaJsonResource
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
