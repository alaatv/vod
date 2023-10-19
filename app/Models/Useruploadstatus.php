<?php

namespace App\Models;


class Useruploadstatus extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'displayName',
        'order',
    ];

    public function useruploads()
    {
        return $this->hasMany(Userupload::class);
    }
}
