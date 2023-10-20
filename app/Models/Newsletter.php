<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Newsletter extends BaseModel
{
    use DateTrait;
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'mobile';
    protected $fillable = [
        'mobile',
        'first_name',
        'last_name',
        'grade_id',
        'major_id',
        'event_id',
        'comment',
    ];

    protected $with = [
        'grade',
        'major',
        'event',
    ];

    public function scopeMobile($query, string $mobile)
    {
        return $query->where('mobile', $mobile);
    }

    public function scopeEventId($query, int $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeEventIds($query, array $eventIds)
    {
        return $query->whereIn('event_id', $eventIds);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
