<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Paymentmethod;
use Illuminate\Http\Request;


/**
 * Class PaymentMethodLightResource
 *
 * @mixin Paymentmethod
 * */
class PaymentMethodLightResource extends AlaaJsonResource
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
        if (!($this->resource instanceof Paymentmethod)) {
            return [];
        }

        return [
            'name' => $this->when(isset($this->name), $this->name),
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
        ];
    }
}
