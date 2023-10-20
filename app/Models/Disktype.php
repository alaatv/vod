<?php

namespace App\Models;


class Disktype extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function disks()
    {
        return $this->hasMany(Disk::class);
    }
}
