<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Transactiongateway;
use Illuminate\Http\Request;


/**
 * Class TransactionGatewayLightResource
 *
 * @mixin Transactiongateway
 * */
class TransactionGatewayLightResource extends AlaaJsonResource
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
        if (!($this->resource instanceof Transactiongateway)) {
            return [];
        }

        return [
            'name' => $this->when(isset($this->name), $this->name),
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
        ];
    }
}
