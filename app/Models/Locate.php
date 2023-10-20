<?php

namespace App\Models;


class Locate extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'globalCode',
        'lft',
        'rgt',
        'lvl',
        'parent_id',
        'published',
    ];
}
