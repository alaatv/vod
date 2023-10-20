<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\AttributeControl;
use App\Http\Resources\AttributeType;
use App\Models\Attribute;
use Illuminate\Http\Request;


/**
 * Class AttributeResource
 *
 * @mixin Attribute
 */
class AttributeResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Attribute)) {
            return [];
        }

        $this->loadMissing('attributecontrol', 'attributetype');

        return [
            'id' => $this->id,
            'attribute_control' => $this->when(isset($this->attributecontrol_id), function () {
                return new AttributeControl($this->attributecontrol);
            }),
            'name' => $this->when(isset($this->name), $this->name),
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
            'description' => $this->when(isset($this->description), $this->description),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'attribute_type' => $this->when(isset($this->attributetype_id), function () {
                return new AttributeType($this->attributetype);
            }),
        ];
    }
}

