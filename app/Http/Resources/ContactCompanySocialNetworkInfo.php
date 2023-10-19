<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ContactCompanySocialNetworkInfo extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array = $this->resource;
        return [
            'name' => $this->when(Arr::has($array, 'name'), Arr::has($array, 'name') ? Arr::get($array, 'name') : null),
            'link' => $this->when(Arr::has($array, 'link'), Arr::has($array, 'link') ? Arr::get($array, 'link') : null),
//            'logo'  => $this->when(Arr::has($array , 'logo') , Arr::has($array , 'logo')?Arr::get($array , 'logo'):null),
            'admin' => $this->when(Arr::has($array, 'admin'),
                Arr::has($array, 'admin') ? Arr::get($array, 'admin') : null),
        ];
    }
}
