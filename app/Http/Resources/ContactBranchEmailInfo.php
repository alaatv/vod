<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class ContactBranchEmailInfo extends AlaaJsonResource
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
            'address' => $this->when(isset($this->address), isset($this->address) ? $this->address : null),
            'description' => $this->when(isset($this->description),
                isset($this->description) ? $this->description : null),
        ];
    }
}
