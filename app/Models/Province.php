<?php

namespace App\Models;


class Province extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
