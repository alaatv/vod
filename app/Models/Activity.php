<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    use DateTrait;

    public const INDEX_PAGE_NAME = 'activityLogPage';

    public function getChangeAttribute()
    {
        return json_decode($this->properties);
    }

    public function scopeBonyadUser(Builder $query)
    {
        return $query->where('log_name', config('activitylog.log_names.bonyad_user'))->pluck('subject_id');
    }

    public function scopeCausedByIds(Builder $query, $causer_ids)
    {
        return $query->whereIn('causer_id', $causer_ids);
    }


}
