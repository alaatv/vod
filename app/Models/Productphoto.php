<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use App\Traits\MinioPhotoHandler;
use Illuminate\Database\Eloquent\Builder;

class Productphoto extends BaseModel
{
    use MinioPhotoHandler;

    public const PHOTO_FIELD = 'file';
    public const DISK = 'productImageMinio';
    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'file',
        'product_id',
        'order',
    ];

    protected $hidden = [
        'id',
        'file',
        'order',
        'deleted_at',
        'created_at',
        'enable',
        'updated_at',
        'product_id',
    ];

    protected $appends = [
        'url',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include enable(or disable) Products.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeEnable($query)
    {
        return $query->where('enable', '=', 1);
    }

    public function getUrlAttribute($value): string
    {
        return $this->url('1400', '2000');
    }

    public function url($w, $h)
    {
        $defaultResult = '/acm/image/255x255.png';

        if (!isset($this->file) || empty($this->file)) {
            return $defaultResult;
        }

        return Uploader::url(Productphoto::DISK, $this->file) ?? $defaultResult;
    }
}
