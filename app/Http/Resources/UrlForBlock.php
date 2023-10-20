<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class UrlForBlock extends AlaaJsonResource
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
            'web' => $this->when(isset($this->url_v2), $this->url),
            'api' => $this->when(isset($this->url_v2), $this->url_v2),
        ];
    }
}
