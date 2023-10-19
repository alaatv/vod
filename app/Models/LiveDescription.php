<?php

namespace App\Models;

use App\Classes\Taggable;
use App\Classes\Uploader\Uploader;
use App\Traits\LiveDescription\TaggableLiveDescriptionTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LiveDescription extends BaseModel implements Taggable
{
    use TaggableLiveDescriptionTrait;
    use LogsActivity;

    protected const LOG_ATTRIBUTES = [
        'entity_type',
        'entity_id',
        'tags',
        'title',
        'description',
    ];
    protected static $recordEvents = ['updated', 'created', 'deleted'];
    protected $table = 'livedescriptions';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'tags',
        'title',
        'description',
        'seen_counter',
        'photo',
        'owner',
        'pinned_at'
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_livedescriptions', 'livedescription_id');
    }

    public function setTagsAttribute($value)
    {
        $data = [
            'bucket' => 'liveDescription',
            'tags' => isset($value) ? convertTagStringToArray($value) : []
        ];
        $this->attributes['tags'] = json_encode($data);
    }

    public function getTagsAttribute($value)
    {
        return $value ? json_decode($value)?->tags : [];
    }

    public function getSeenCounterAttribute($value)
    {
        return $value ?? 0;
    }

    public function isTaggableActive(): bool
    {
        return true; // use for TaggableTrait
    }

    public function getPhotoAttribute($value)
    {
        if (isset($this->product) && isset($this->product->photo) && !empty($this->product->photo)) {
            return $this->product->photo;
        }

        if (empty($value)) {
            return null;
        }

        return Uploader::url(config('disks.LIVE_DESCRIPTION_MINIO'), $value);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $model = explode('\\', self::class)[1];
        $console_description = ' from console';

        return LogOptions::defaults()
            ->logOnly(self::LOG_ATTRIBUTES)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName
            ) => (auth()->check()) ? $eventName : $eventName.$console_description)
            ->useLogName("{$model}");
    }

    //region scopes
    public function scopeCreatedAfter($query, string $createdAfter)
    {
        return $query->where('created_at', '>=', $createdAfter);
    }
    //endregion
}
