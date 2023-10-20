<?php

namespace App\Models;

class Producttype extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
