<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use Illuminate\Http\Request;


class ProductvoucherResource extends AlaaJsonResource
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
            'contractor' => $this->contractor_id,
            'products' => $this->products,
            'user' => $this->user_id,
            'order' => $this->order_id,
            'used_at' => $this->used_at,
            'code' => $this->code,
            'package_name' => $this->package_name,
            'expirationdatetime' => $this->expirationdatetime,
            'enable' => $this->enable,
            'description' => $this->description,

        ];
    }
}
