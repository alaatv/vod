<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\ProductType;
use Illuminate\Http\Request;


/**
 * Class ProductTypeResource
 *
 * @mixin Producttype
 */
class ProductTypeResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Producttype)) {
            return [];
        }

        return [
            'id' => $this->id,
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
            'name' => $this->when(isset($this->name), $this->name),
            'description' => $this->when(isset($this->description), $this->description),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
        ];
    }
}

