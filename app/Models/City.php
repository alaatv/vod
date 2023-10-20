<?php

namespace App\Models;


class City extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'province_id',
        'name',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
