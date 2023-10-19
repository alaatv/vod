<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class GatewayResource extends AlaaJsonResource
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
            'name' => $this->name,
            'displayName' => $this->displayName,
            'description' => $this->description,
            'order' => $this->order,
            'photo' => 'https://alaatv.com/acm/extra/payment/gateway/'.$this->icon,
        ];
    }
}
