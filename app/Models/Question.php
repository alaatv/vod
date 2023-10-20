<?php

namespace App\Models;


class Question extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'control_id',
        'dataSourceUrl',
        'querySourceUrl',
        'statement',
        'description',
    ];

    public function control()
    {
        return $this->belongsTo(Attributecontrol::class);
    }

    public function surveys()
    {
        return $this->belongsToMany(Survey::class)
            ->withPivot('order', 'enable', 'description');
    }

    public function usersurveyasnwer()
    {
        return $this->hasMany(Usersurveyanswer::class);
    }
}
