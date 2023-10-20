<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyEventReport extends Model
{
    use HasFactory;

    public const READ_REPORT = 1;
    public const UN_READ_REPORT = 0;
    protected $fillable = [
        'user_id',
        'study_event_id',
        'report',
        'is_read',
    ];
    protected $casts = [
        'report' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studyEvent()
    {
        return $this->belongsTo(Studyevent::class, 'study_event_id');
    }

    public function scopeRead($query, $value)
    {
        return $query->where('is_read', $value);
    }
}
