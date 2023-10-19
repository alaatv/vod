<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-03-08
 * Time: 20:25
 */

namespace App\Traits\Product;

use App\Classes\Uploader\Uploader;
use App\Models\Productphoto;
use App\Traits\MinioPhotoHandler;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait ProductPhotoTrait
{
    use MinioPhotoHandler;

    /**
     * Gets product's root image (image from it's grand parent)
     *
     * @return string
     */
    public function getRootImage(): string
    {
        $key = 'product:rootImage:'.$this->cacheKey();

        return Cache::tags(['product', 'photo', 'productPhoto', 'product_'.$this->id, 'product_'.$this->id.'_photo'])
            ->remember($key, config('constants.CACHE_600'), function () {
                $image = '';
                $grandParent = $this->grandParent;
                if (isset($grandParent)) {
                    if (isset($grandParent->image)) {
                        $image = $grandParent->image;
                    }
                } else {
                    if (isset($this->image)) {
                        $image = $this->image;
                    }
                }

                return $image;
            });
    }

    public function getPhotoAttribute()
    {
        $defaultResult = '/acm/image/255x255.png';

        if (!isset($this->image) || empty($this->image)) {
            return $defaultResult;
        }

        $this->setDisk();
        return Uploader::url($this->disk, $this->image) ?? $defaultResult;
    }

    public function getWidePhotoAttribute()
    {
        $defaultResult = '/acm/image/255x255.png';

        if (!isset($this->wide_image) || empty($this->wide_image)) {
            return $defaultResult;
        }

        $this->setDisk();
        return Uploader::url($this->disk, $this->wide_image) ?? $defaultResult;
    }

    /**
     * Makes a collection of product phoots
     *
     */
    public function getSamplePhotosAttribute(): ?Collection
    {
        $key = 'product:SamplePhotos:'.$this->cacheKey();
        $productSamplePhotos =
            Cache::tags(['product', 'photo', 'samplePhoto', 'product_'.$this->id, 'product_'.$this->id.'_samplePhotos'])
                ->remember($key, config('constants.CACHE_60'), function () {
                    $photos = collect();

                    $thisPhotos = $this->photos()
                        ->enable()
                        ->get();

                    $photos = $photos->merge($thisPhotos);
                    $allChildren = $this->getAllChildren();
                    foreach ($allChildren as $child) {
                        $childPhotos = $child->photos()
                            ->enable()
                            ->get();
                        $photos = $photos->merge($childPhotos);
                    }

                    return $photos->sortBy('order')
                        ->values();
                });

        return $productSamplePhotos->count() > 0 ? $productSamplePhotos : null;
    }

    /**
     * @return HasMany|Productphoto
     */
    public function photos()
    {
        return $this->hasMany(Productphoto::Class);
    }
}
