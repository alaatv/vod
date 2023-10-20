<?php

namespace App\Models;


class Attributegroup extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'attributeset_id',
    ];

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class)
            ->withPivot('order', 'description')
            ->withTimestamps()
            ->orderBy('order');
    }

    public function attributeset()
    {
        return $this->belongsTo(Attributeset::class);
    }
}
