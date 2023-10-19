<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Ostan extends Model
{
    protected $table = 'ostan';

    public function allShahr()
    {
        return $this->hasMany(Shahr::class)->where('type', 0);
    }
}
