<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class ContactBranchAddressInfo extends AlaaJsonResource
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
            'city' => $this->when(isset($this->city), isset($this->city) ? $this->city : null),
            'street' => $this->when(isset($this->street), isset($this->street) ? $this->street : null),
            'avenue' => $this->when(isset($this->avenue), isset($this->avenue) ? $this->avenue : null),
            'extra' => $this->when(isset($this->extra), isset($this->extra) ? $this->extra : null),
            'postalCode' => $this->when(isset($this->postalCode), isset($this->postalCode) ? $this->postalCode : null),
        ];
    }
}
