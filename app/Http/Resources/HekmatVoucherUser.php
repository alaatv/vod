<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\User
 *
 * @mixin \App\User
 *
 */
class HekmatVoucherUser extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\User)) {
            return [];
        }

        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
        ];
    }
}
