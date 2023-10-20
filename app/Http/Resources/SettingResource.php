<?php

namespace App\Http\Resources;


use App\Models\Setting;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class SettingResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Setting)) {
            return [];
        }

        return [
            'service_id' => $this->service_id,
            'key' => $this->key,
            'value' => json_decode($this->value),
        ];
    }
}
