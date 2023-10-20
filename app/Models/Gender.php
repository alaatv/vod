<?php

namespace App\Models;

class Gender extends BaseModel
{
    public const BOY = 1;
    public const GIRL = 2;
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
