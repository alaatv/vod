<?php

namespace App\Models;

class StudyEventNews extends BaseModel
{
    protected $table = 'studyEventNews';

    protected $fillable = [
        'studyevent_id',
        'title',
        'body',
    ];

    //region relations
    public function studyevent()
    {
        return $this->belongsTo(Studyevent::class);
    }
    //endregion
}
