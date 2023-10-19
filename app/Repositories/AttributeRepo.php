<?php

namespace App\Repositories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Builder;

class AttributeRepo
{
    public static function getAttributesByType(string $attributeType): Builder
    {
        return Attribute::where('attributetype_id', $attributeType);
    }
}
