<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class Gender
 *
 * @mixin \App\Models\Gender
 * */
class Gender extends AlaaJsonResource
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
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name), //We should keep it for Andoid app
            'title' => $this->when(isset($this->name), $this->name),
        ];
    }
}
