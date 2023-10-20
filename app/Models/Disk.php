<?php

namespace App\Models;


class Disk extends BaseModel
{
    protected $fillable = [
        'name',
        'disktype_id',
    ];

    public function disktype()
    {
        return $this->belongsTo(Disktype::class);
    }

    public function files()
    {
        return $this->belongsToMany(File::class)
            ->withPivot('priority');
    }
}
