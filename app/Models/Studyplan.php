<?php

namespace App\Models;


class Studyplan extends BaseModel
{
    protected $fillable = [
        'event_id',
        'row',
        'voice',
        'body',
        'title',
        'date',
        'plan_date',
    ];

    protected $with = ['plans'];

    public function plans()
    {
        return $this->hasMany(Plan::Class);
    }

    public function event()
    {
        return $this->belongsTo(Studyevent::Class, 'event_id');
    }

    public function contents()
    {
        return $this->belongsToMany(
            Content::class,
            'educationalcontent_studyplan',
            'studyplan_id',
            'content_id',
            'id',
            'id'
        )->withTimestamps()->withPivot('type_id');
    }
}
