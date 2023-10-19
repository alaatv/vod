<?php

namespace App\Models;


class Phonetype extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
        'isEnable',
    ];

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }
}
