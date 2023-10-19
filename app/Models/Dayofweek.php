<?php

namespace App\Models;


class Dayofweek extends BaseModel
{
    protected $table = 'dayofweek';

    /**      * The attributes that should be mutated to dates.        */
    protected $casts = [
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'deleted_at'=> 'datetime',
    ];

    protected $fillable = [
        'name',
        'displayName',
    ];

    public function lives()
    {
        return $this->hasMany(Live::Class);
    }
}
