<?php

namespace App\Models;


class Usersurveyanswer extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'question_id',
        'survey_id',
        'event_id',
        'answer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
