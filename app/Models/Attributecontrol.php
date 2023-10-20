<?php

namespace App\Models;


class Attributecontrol extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
