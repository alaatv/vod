<?php

namespace App\Http\Resources;

use App\Models\Websitesetting;
use Illuminate\Http\Request;

class WebsiteSettingResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'setting' => $this->setting,
        ];
    }

    public function resolve($request = null): array
    {
        if (!($this->resource instanceof Websitesetting)) {
            return [];
        }
        return parent::resolve($request);
    }
}
