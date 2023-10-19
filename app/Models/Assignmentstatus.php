<?php

namespace App\Models;



class Assignmentstatus extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
