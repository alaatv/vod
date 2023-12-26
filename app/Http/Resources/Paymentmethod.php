<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class Paymentmethod
 *
 * @mixin \App\Models\Paymentmethod
 * */
class Paymentmethod extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (! ($this->resource instanceof \App\Models\Paymentmethod)) {
            return [];
        }

        return [
            'name' => $this->when(isset($this->name), $this->name),
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
            'id' => $this->when(isset($this->id), $this->id),
        ];
    }
}
