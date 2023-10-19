<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class ContactBranchPhoneInfo extends AlaaJsonResource
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
            'number' => $this->when(isset($this->number), isset($this->number) ? $this->number : null),
            'description' => $this->when(isset($this->description),
                isset($this->description) ? $this->description : null),
        ];
    }
}
