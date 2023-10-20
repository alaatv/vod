<?php

namespace App\Models;


class MapDetailType extends BaseModel
{
    protected $table = 'mapDetailTypes';

    protected $fillable = [
        'id',
        'title',
    ];

    public function mapDetails()
    {
        return $this->hasMany(MapDetail::class);
    }
}
