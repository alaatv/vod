<?php

namespace App\Models;

class Afterloginformcontrol extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'order',
    ];

    public static function getFormFields()
    {
        return Afterloginformcontrol::all()
            ->where('enable', 1)
            ->sortBy('order');
    }
}
