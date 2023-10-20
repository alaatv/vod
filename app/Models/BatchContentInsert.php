<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchContentInsert extends Model
{
    use DateTrait;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uploaded_file',
        'downloadable_file',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute($value)
    {
        return match ($value) {
            'success' => 'موفق',
            'failed' => 'ناموفق',
            'processing' => 'در حال پردازش',
        };
    }

    public function getDownloadableFileAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Uploader::url(config('disks.BATCH_CONTENT_INSERT'), $value);
    }
}
