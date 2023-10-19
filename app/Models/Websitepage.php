<?php

namespace App\Models;

class Websitepage extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'url',
        'displayName',
        'description',
    ];

    public function userschecked()
    {//Users that have seen this site page
        return $this->belongsToMany(User::class, 'userseensitepages', 'websitepage_id', 'user_id');
    }

    public function slides()
    {
        return $this->hasMany(Slideshow::class);
    }
}
