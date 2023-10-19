<?php

namespace App\Models;


class Survey extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class)
            ->withPivot('order', 'enable', 'description');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('order', 'enable', 'description');
    }

    public function usersurveyanswer()
    {
        return $this->hasMany(Usersurveyanswer::class);
    }
}
