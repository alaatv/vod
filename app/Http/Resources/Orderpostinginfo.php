<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\Orderpostinginfo
 *
 * @mixin \App\Orderpostinginfo
 * */
class Orderpostinginfo extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Orderpostinginfo)) {
            return [];
        }


        return [
            'post_code' => $this->when(isset($this->postCode), $this->postCode),
        ];
    }
}
