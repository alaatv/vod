<?php

namespace App\Models;


class Studyevent extends BaseModel
{
    public const ARASH_STUDY_EVENT_ID = 2;
    public const ARASH_MORDAD_STUDY_EVENT_ID = 3;
    public const TAFTAN1400_STUDY_EVENT_ID = 4;
    public const ARASH_STUDY_EVENT_MAP = [
        self::ARASH_STUDY_EVENT_ID => '2020-06-30',
        self::ARASH_MORDAD_STUDY_EVENT_ID => '2020-07-25',
    ];
    public const ABRISHAM_2_CHANGE_LIMIT = 10;
    public const ABRISHAM_1401 = 5;

    protected $fillable = [
        'title',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function studyplans()
    {
        return $this->hasMany(Studyplan::Class, 'event_id');
    }

    public function livedescriptions()
    {
        return $this->morphMany(LiveDescription::class, 'entity');
    }

    public function studyEventMethod()
    {
        return $this->belongsTo(StudyEventMethod::class, 'method_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function scopeFindByMethodAndMajorAndGrade($query, $methodId, $majorId, $gradeId)
    {
        return $query->where('method_id', $methodId)->where('major_id', $majorId)->where('grade_id', $gradeId);
    }

    public function watchHistories()
    {
        return $this->hasMany(WatchHistory::class, 'studyevent_id');
    }

    public function studyEventReports()
    {
        return $this->hasMany(StudyEventReport::class, 'study_event_id');
    }
}
