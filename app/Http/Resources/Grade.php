<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Grade
 *
 * @mixin \App\Grade
 * */
class Grade extends AlaaJsonResource
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
        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->displayName), $this->displayName),//We should keep it for Andoid app
            'title' => $this->when(isset($this->displayName), $this->displayName),
        ];
    }
}
