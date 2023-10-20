<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vast extends Model
{
    use HasFactory;

    public const INDEX_PAGE_NAME = 'vastPage';
    public const VAST_TEMPLATE_XML = 'vast-template.xml';
    public const VIDEO_QUALITY_CAPTIONS = [
        'HD_720p' => 'کیفیت عالی',
        'hq' => 'کیفیت بالا',
        '240p' => 'کیفیت متوسط',
    ];
    public const QUALITY_DISK_MAP = [
        'hq' => 'VAST_VIDEO_HQ_MINIO',
        'HD_720p' => 'VAST_VIDEO_HD_720P_MINIO',
        '240p' => 'VAST_VIDEO_240P_MINIO',
    ];
    protected $fillable = [
        'title',
        'is_default',
        'enable',
        'file_url',
        'more_info_link',
        'click_id',
        'click_name',
        'files',
    ];

    public function contents()
    {
        return $this->belongsToMany(Content::class, 'content_vast', 'vast_id', 'content_id')
            ->withPivot('created_at')
            ->orderBy('created_at', 'desc');
    }

    public function sets()
    {
        return $this->belongsToMany(Contentset::class, 'set_vast', 'vast_id', 'set_id')
            ->withPivot('created_at')
            ->orderBy('created_at', 'desc');
    }

    public function scopeEnable($q)
    {
        return $q->where('enable', 1);
    }

    public function scopeIsDefault($q)
    {
        return $q->where('is_default', 1);
    }

    public function getFilesAttribute($files)
    {
        $files = json_decode($files);
        foreach ($files as $file) {
            $file->url = Uploader::url($file->disk, $file->name);
        }
        return $files;
    }

    public function getUrlAttribute()
    {
        return Uploader::url(config('disks.VAST_XML_MINIO'), $this->file_url);
    }
}
