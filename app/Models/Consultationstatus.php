<?php

namespace App\Models;


class Consultationstatus extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}
