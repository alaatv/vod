<?php

namespace App\Models;


class Map extends BaseModel
{
    protected $fillable =
        [
            'title',
        ];

    public function mapDetails()
    {
        return $this->hasMany(MapDetail::class);
    }
}
