<?php

namespace App\Models;

class Articlecategory extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'enable',
        'order',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
