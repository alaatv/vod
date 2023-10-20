<?php

namespace App\Models;

class Grade extends BaseModel
{
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function contents()
    {
        return $this->belongsToMany(Content::class);
    }

    public function newsLetters()
    {
        return $this->hasMany(Newsletter::class);
    }
}
