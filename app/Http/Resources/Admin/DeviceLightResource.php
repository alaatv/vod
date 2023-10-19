<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Device;
use App\Models\Device;
use Illuminate\Http\Request;


/**
 * Class DeviceLightResource
 *
 * @mixin Device
 * */
class DeviceLightResource extends AlaaJsonResource
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
        if (!($this->resource instanceof Device)) {
            return [];
        }

        return [
            'name' => $this->when(isset($this->name), $this->name),
            'display_name' => $this->when(isset($this->display_name), $this->display_name),
        ];
    }
}
