<?php

namespace App\Models;


class Userbonstatus extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    public function userbons()
    {
        return $this->hasMany(Userbon::class);
    }
}
