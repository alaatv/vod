<?php

namespace App\Models;

class Template extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
