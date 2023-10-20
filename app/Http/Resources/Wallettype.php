<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Wallettype
 *
 * @mixin \App\Wallettype
 * */
class Wallettype extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Wallettype)) {
            return [];
        }

        return [
            'name' => $this->displayName,
        ];
    }
}
