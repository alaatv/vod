<?php

namespace App\Models;


class Bontype extends BaseModel
{
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function bons()
    {
        return $this->hasMany(Bon::class);
    }
}
