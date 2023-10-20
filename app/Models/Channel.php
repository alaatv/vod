<?php

namespace App\Models;

use App\Classes\SEO\SeoInterface;
use App\Classes\Uploader\Uploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Channel extends BaseModel implements SeoInterface
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'thumbnail'];

    protected $with = [
        'blocks',
    ];

    public function blocks()
    {
        return $this->belongsToMany(Block::class);
    }

    public function getUrlAttribute($value): string
    {
        if (isset($this->id)) {
            return appUrlRoute('ch.show', $this);
        }
        return '';
    }

    public function getApiUrlV2Attribute($value)
    {
        return appUrlRoute('api.ch.show', $this->id);
    }

    public function getMetaTags(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'url' => route('ch.show', $this->id),
            'canonical' => route('ch.show', $this->id),
            'site' => 'آلاء',
            'imageUrl' => $this->thumbnail,
            'imageWidth' => '1280',
            'imageHeight' => '720',
        ];
    }

    public function getThumbnailAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Uploader::url(config('disks.CHANNEL_THUMBNAIL_MINIO'), $value);
    }


}
