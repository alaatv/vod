<?php

namespace App\Models;


class Majortype extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function majors()
    {
        return $this->hasMany(Major::class);
    }
}
