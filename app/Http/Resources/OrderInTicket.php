<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Order
 *
 * @mixin \App\Order
 * */
class OrderInTicket extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Order)) {
            return [];
        }

        return [
            'id' => $this->id,
        ];
    }
}
