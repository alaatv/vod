<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use App\Traits\MinioPhotoHandler;

class Descriptionwithperiod extends BaseModel
{
    use MinioPhotoHandler;

    public const PHOTO_FIELD = 'photo';

    protected $fillable = [
        'product_id',
        'staff_id',
        'description',
        'since',
        'till',
        'photo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
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

        return Uploader::url(config('disks.PERIOD_DESCRIPTION_PHOTO_MINIO'), $value);
    }

    /*
    |--------------------------------------------------------------------------
    | relations
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::Class);
    }

    public function user()
    {
        return $this->belongsTo(User::Class);
    }

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    */
    /**
     * Converting since field to Jalali
     *
     * @param  bool  $withTime
     *
     * @return string
     */
    public function Since_Jalali($withTime = false): string
    {
        $since = $this->since;
        $explodedDateTime = explode(' ', $since);
        $explodedTime = $explodedDateTime[1];
        $explodedDate = $this->convertDate($since, 'toJalali');

        if (!$withTime) {
            return $explodedDate;
        }
        return ($explodedDate.' '.$explodedTime);

    }

    /**
     * Converting till field to Jalali
     *
     * @param  bool  $withTime
     *
     * @return string
     */
    public function Until_Jalali($withTime = false): string
    {
        $till = $this->till;
        $explodedDateTime = explode(' ', $till);
        $explodedTime = $explodedDateTime[1];
        $explodedDate = $this->convertDate($till, 'toJalali');

        if (!$withTime) {
            return $explodedDate;
        }
        return ($explodedDate.' '.$explodedTime);

    }

    public function getImageAttribute()
    {
        return $this->getRawOriginal('photo');
    }
}
