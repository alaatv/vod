<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\Firebasetoken
 *
 * @mixin \App\Firebasetoken
 * */
class Firebasetoken extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                'refresh_token' => $this->refresh_token,
            ];
    }
}
