<?php

namespace App\Models;


class Contact extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
        'contacttype_id',
        'relative_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relative()
    {
        return $this->belongsTo(Relative::class);
    }

    public function contacttype()
    {
        return $this->belongsTo(Contacttype::class);
    }

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }
}
