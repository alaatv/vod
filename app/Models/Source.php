<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use App\Traits\MinioPhotoHandler;

class Source extends BaseModel
{
    use MinioPhotoHandler;

    public const PHOTO_FIELD = 'photo';

    protected $fillable = [
        'title',
        'link',
        'photo',
    ];

    /*
    |--------------------------------------------------------------------------
    | mutators
    |--------------------------------------------------------------------------
    */

    public function getPhotoAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Uploader::url(config('disks.SOURCE_PHOTO_MINIO'), $value);
    }

    /*
    |--------------------------------------------------------------------------
    | relations
    |--------------------------------------------------------------------------
    */

    public function sets()
    {
        return $this->morphedByMany(Contentset::class, 'sourceable')
            ->withTimestamps()
            ->withPivot(['order'])
            ->orderBy('sourceable.order');
    }

    public function contents()
    {
        return $this->morphedByMany(Content::class, 'sourceable')
            ->withTimestamps()
            ->withPivot(['order'])
            ->orderBy('sourceable.order');
    }


    public function getImageAttribute()
    {
        return $this->getRawOriginal('photo');
    }
}
