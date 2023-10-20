<?php

namespace App\Models;


class School extends BaseModel
{
    protected $fillable = [
        'schoolType_id',
        'code',
        'phone',
        'province_id',
        'city_id',
        'address',
        'managerName',
    ];
}
