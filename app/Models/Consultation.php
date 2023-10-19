<?php

namespace App\Models;


class Consultation extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'videoPageLink',
        'textScriptLink',
        'order',
        'enable',
        'consultationstatus_id',
    ];

    public function consultationstatus()
    {
        return $this->belongsTo(Consultationstatus::class);
    }

    public function majors()
    {
        return $this->belongsToMany(Major::class)
            ->withTimestamps();
    }
}
