<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'watchable_id',
        'watchable_type',
        'seconds_watched',
        'studyevent_id',
        'completely_watched',
    ];
    protected $casts = [
        'completely_watched' => 'boolean',
    ];
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the parent watchable model (Content, ContentSet, Product and ...)
     *
     * @return MorphTo
     */
    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function studyEvent()
    {
        return $this->belongsTo(Studyevent::class, 'studyevent_id');
    }

    /**
     * @param  Builder  $query
     * @param  string  $exp
     * @return Builder
     */
    public function scopeWatchableType(Builder $query, string $exp): Builder
    {
        return $query->where('watchable_type', config("constants.MORPH_MAP_MODELS.{$exp}.model"));
    }
}
