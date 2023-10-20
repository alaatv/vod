<?php

namespace App\Models;


class Employeeovertimestatus extends BaseModel
{
    public const CONFIRMED_ID = 2;
    protected $table = 'employeeovertimestatus';
    protected $fillable = [
        'name',
        'display_name',
    ];

    public function employeetimesheet()
    {
        return $this->hasMany(Employeetimesheet::Class, 'overtime_status_id', 'id');
    }
}
