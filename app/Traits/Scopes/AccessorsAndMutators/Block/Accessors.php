<?php

namespace App\Traits\Scopes\AccessorsAndMutators\Block;

use Illuminate\Support\Facades\Cache;

trait Accessors
{
    public function getOfferAttribute($value)
    {
        return $this->isOfferBlock;
    }

    public function getUrlAttribute($value): ?string
    {
        if (isset($this->customUrl)) {
            return $this->customUrl;
        }

        if (is_null($this->tags)) {
            return null;
        }

        return isset(static::ACTION_LOOKUP_TABLE[$this->type]) ? $this->makeUrl(static::ACTION_LOOKUP_TABLE[$this->type],
            $this->tags) : null;
    }

    public function getUrlV2Attribute($value): ?string
    {
        if (isset($this->customUrl)) {
            return $this->customUrl;
        }

        return isset(static::ACTION_LOOKUP_TABLE_V2[$this->type]) ? $this->makeUrl(static::ACTION_LOOKUP_TABLE_V2[$this->type],
            $this->tags) : null;
    }

    public function getActiveBannersAttribute()
    {
        $key = 'activeBanners:'.'block:'.$this->id;
        $tags = ['banner', 'banner_'.$this->id];
        if (Cache::tags($tags)->has($key)) {
            return Cache::tags(['banner', 'banner_'.$this->id])->get($key);
        }
        $activeSlideshows = $this->banners()->active()->get();
        $ttl = self::calculateExpireTimeForCachingSlides($this, $activeSlideshows);

        Cache::tags($tags)->put($key, $activeSlideshows, $ttl);

        return $activeSlideshows;
    }
}
