<?php

namespace App\Models;


class Relative extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function contact()
    {
        return $this->hasMany(Contact::class);
    }
}
