<?php

namespace App\Models;


class Live extends BaseModel
{
    protected $table = 'liveschedules';

    /**      * The attributes that should be mutated to dates.        */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'dayofweek_id',
        'title',
        'description',
        'poster',
        'start_time',
        'finish_time',
        'first_live',
        'last_live',
        'enable',
    ];

    protected $hidden = [
        'dayofweek',
    ];

    public function dayOfWeek()
    {
        return $this->belongsTo(Dayofweek::Class, 'dayofweek_id', 'id');
    }

    public function scopeEnable($query)
    {
        return $query->where('enable', 1);
    }
}
