<?php

namespace App\Models;

class Plan extends BaseModel
{
    protected $fillable = [
        'studyplan_id',
        'major',
        'major_id',
        'lesson_name',
        'title',
        'section_name',
        'description',
        'offer',
        'description',
        'long_description',
        'link',
        'voice',
        'video',
        'time',
        'start',
        'end',
        'background_color',
        'border_color',
        'text_color',
    ];

    public function studyplan()
    {
        return $this->belongsTo(Studyplan::Class);
    }

    public function contents()
    {
        return $this->belongsToMany(
            Content::class,
            'educationalcontent_plan',
            'plan_id',
            'content_id',
            'id',
            'id'
        )->withTimestamps()->withPivot('type_id');
    }

    public function type()
    {
        return $this->belongsToMany(
            ContentOfPlanType::class,
            'educationalcontent_plan',
            'plan_id',
            'type_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
