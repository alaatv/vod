<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class ContactBranchFaxInfo extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->resource;
        return [
            'number' => $resource,
        ];
    }
}
