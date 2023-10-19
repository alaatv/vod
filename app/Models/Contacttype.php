<?php

namespace App\Models;


class Contacttype extends BaseModel
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

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
