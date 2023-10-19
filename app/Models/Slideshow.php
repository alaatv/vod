<?php

namespace App\Models;

use App\Adapter\AlaaSftpAdapter;
use App\Classes\Uploader\Uploader;
use App\Collection\SlideshowCollection;
use App\Traits\MinioPhotoHandler;
use App\Traits\SlideShowCommon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Slideshow extends BaseModel
{
    use MinioPhotoHandler;
    use SlideShowCommon;

    public const PHOTO_FIELD = 'photo';
    public const HOME_PAGE_ID = 25;
    public const INDEX_PAGE_NAME = 'slideshowPage';
    public const BANNERS_TWO_PACK_LEFT_LINKS = [
        242 => 'https://alaatv.com/landing/15',
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'shortDescription',
        'photo',
        'link',
        'order',
        'isEnable',
        'in_new_tab',
        'validSince',
        'validUntil',
        'width',
        'height',
        'screensize_id'
    ];
    protected $appends = [
        'url',
    ];
    protected $hidden = [
        'photo',
        'isEnable',
        'deleted_at',
        'created_at',
    ];

    public function getUrlAttribute($value): string
    {
        /** @var AlaaSftpAdapter $diskAdapter */
        $imageUrl = $this->photo;
        return isset($imageUrl) ? $imageUrl.'?w=1280&h=500' : '/acm/image/255x255.png';

//        return route('image', ['category' => 9, 'w' => '1280', 'h' => '500', 'filename' => $this->photo]);
    }

    public function getUrlForWebAttribute($value): string
    {
        if (isset($this->photo) && isset($this->width) && isset($this->height)) {
            return "{$this->photo}?w={$this->width}&h={$this->height}";
        }

        // TODO: The follow image isn't valid. Because doesn't display on the site slideshow
        return '/acm/image/255x255.png';
    }

    /**
     * @return float|null
     */
    public function getRatioAttribute(): ?float
    {
        if (isset($this->photo) && isset($this->width) && isset($this->height)) {
            return $this->width / $this->height;
        }

        return null;
    }

    /**
     * @return MorphToMany
     */
    public function blocks(): MorphToMany
    {
        return $this->morphToMany(Block::class, 'blockable')->withTimestamps()->withPivot(['order']);
    }

    /**
     * @return string
     * Converting Created_at field to jalali
     */
    public function slideshowCreatedAtJalali()
    {
        if (!isset($this->created_at)) {

            return 'نا مشخص';
        }
        $explodedDateTime = explode(' ', $this->created_at);
        if (strcmp($explodedDateTime[0], '0000-00-00') != 0) {
            $explodedTime = $explodedDateTime[1];

            return $this->convertDate($explodedDateTime[0], 1).' Slideshow.php'.$explodedTime;
        }


        return 'نا مشخص';
    }

    /**
     * @return string
     * Converting Updated_at field to jalali
     */
    public function slideshowUpdatedAtJalali()
    {
        if (!isset($this->updated_at)) {

            return 'نا مشخص';
        }
        $explodedDateTime = explode(' ', $this->updated_at);
        if (strcmp($explodedDateTime[0], '0000-00-00') != 0) {
            $explodedTime = $explodedDateTime[1];

            return $this->convertDate($explodedDateTime[0], 1).' Slideshow.php'.$explodedTime;
        }


        return 'نا مشخص';
    }

    /**
     * Scope a query to only include enable(or disable) Coupons.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeEnable($query)
    {
        return $query;
        //فیلد isEnable از دیتا بیس حذف شده وبرای جلوگیری از خطاها موقتا عملکرد این اسکوپ را به این شکل تغییر دادیم تا در آینده ریفکتور اساسی تر صورت گیرد
    }

    /**
     * Scope a query to only include valid Coupons.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeValid($query)
    {
        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran'));

        return $query->where(function ($q) use ($now) {
            $q->where('validSince', '<=', $now)
                ->orWhereNull('validSince');
        })
            ->where(function ($q) use ($now) {
                $q->where('validUntil', '>=', $now)
                    ->orWhereNull('validUntil');
            });
    }

    public function scopeActive($query)
    {
        return $query->enable()->valid();
    }

    /**
     * Combine Banner's id, title
     *
     * @return string
     */
    public function getItInfoAttribute(): string
    {
        return "#{$this->id}".(empty($this->title) ? '' : " - {$this->title}");
    }

    /**
     * Combine Banner's id, title, shortDescription
     *
     * @return string
     */
    public function getItdInfoAttribute(): string
    {
        return "{$this->it_info}".(empty($this->shortDescription) ? '' : " ({$this->shortDescription})");
    }

    /**
     * @return bool
     */
    public function getIsTwoPackAttribute(): ?bool
    {
        return in_array($this->id, array_keys(self::BANNERS_TWO_PACK_LEFT_LINKS));
    }

    /**
     * @return string|null
     */
    public function getRightLinkAttribute(): ?string
    {
        return $this->link;
    }

    /**
     * @return string|null
     */
    public function getLeftLinkAttribute(): ?string
    {
        if (
            array_key_exists($this->id, self::BANNERS_TWO_PACK_LEFT_LINKS) &&
            isset(self::BANNERS_TWO_PACK_LEFT_LINKS[$this->id]) &&
            !empty(trim(self::BANNERS_TWO_PACK_LEFT_LINKS[$this->id]))
        ) {
            return self::BANNERS_TWO_PACK_LEFT_LINKS[$this->id];
        }
        return null;
    }

    public function getPhotoAttribute($value)
    {
        $defaultResult = '/acm/image/255x255.png';

        if (empty($value)) {
            return $defaultResult;
        }

        return Uploader::url(config('disks.HOME_SLIDESHOW_PIC_MINIO'), $value) ?? $defaultResult;
    }

    public function getImageAttribute()
    {
        return $this->getRawOriginal('photo');
    }

    public function setLinkAttribute($value)
    {
        if (
            strlen($value) != 0 &&
            isset($value) &&
            !preg_match('/^http:\/\//', $value) &&
            !preg_match('/^https:\/\//', $value)
        ) {
            $value = "https://{$value}";
        }

        $this->attributes['link'] = $value;
    }

    public function getIsEnableAttribute()
    {
        return 1;
    }

    public function screensize()
    {
        return $this->belongsTo(Screensize::class, 'screensize_id');
    }
}
