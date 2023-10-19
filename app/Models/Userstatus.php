<?php

namespace App\Models;

class Userstatus extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
