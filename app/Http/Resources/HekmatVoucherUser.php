<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class \App\Models\User
 *
 * @mixin \App\Models\User
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
        if (! ($this->resource instanceof \App\Models\User)) {
            return [];
        }

        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
        ];
    }
}
