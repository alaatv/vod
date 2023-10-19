<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 16:44
 */

namespace App\Traits\User;

use App\Collection\ContentCollection;
use App\Models\Contentset;
use App\Models\Timepoint;
use Illuminate\Support\Facades\Cache;


trait FavoredTrait
{
    /*
    |--------------------------------------------------------------------------
    | relations
    |--------------------------------------------------------------------------
    */
    protected $list_of_favored_contents_cache;

    public function getTotalActiveFavoredContents()
    {
        return Cache::tags([
            'favorite', 'user', 'user_'.$this->id, 'user_'.$this->id.'_favorites',
            'user_'.$this->id.'_favoriteTotalContents'
        ])
            ->remember('user:favorite:totalContents:'.$this->cacheKey(), config('constants.CACHE_10'), function () {
                $contents = $this->getActiveFavoredContents();
                $favoredTimepoints = $this->getActiveFavoredContentTimepoints();
                $totalContents = new ContentCollection();
                foreach ($favoredTimepoints as $favoredTimepoint) {
                    $timepointContent = $favoredTimepoint->content;
                    if (!$contents->contains($timepointContent)) {
                        $timepointContent->isFavored = false;
                    }
                    $totalContents->push($timepointContent);
                }

                $totalContents = $totalContents->merge($contents);
                return $totalContents->unique();
            });
    }

    public function getActiveFavoredContents()
    {
        if (!is_null($this->list_of_favored_contents_cache)) {
            return $this->list_of_favored_contents_cache;
        }
        $this->list_of_favored_contents_cache = Cache::tags([
            'favorite', 'user', 'user_'.$this->id, 'user_'.$this->id.'_favorites', 'user_'.$this->id.'_favoriteContents'
        ])
            ->remember('user:favorite:contents:'.$this->cacheKey(), config('constants.CACHE_10'), function () {
                return $this->favoredContents()
                    ->active()
                    ->notRedirected()
                    ->get()
                    ->sortBy('pivot.created_at');
            });
        return $this->list_of_favored_contents_cache;
    }

    public function favoredContents()
    {
        return $this->morphedByMany(Content::class, 'favorable')->withTimestamps();
    }

    public function getActiveFavoredContentTimepoints()
    {
        return Cache::tags([
            'favorite', 'user', 'user_'.$this->id, 'user_'.$this->id.'_favorites',
            'user_'.$this->id.'_favoriteContentTimepoints'
        ])
            ->remember('user:favorite:contents:'.$this->cacheKey(), config('constants.CACHE_10'), function () {
                return $this->favoredTimepoints()
                    ->get()
                    ->sortBy('pivot.created_at');
            });
    }

    public function favoredTimepoints()
    {
        return $this->morphedByMany(Timepoint::class, 'favorable')->withTimestamps();
    }

    public function getTotalActiveFavoredContentsWithoutCache(
        string $search = null,
        string $contentSetTitleSearch = null
    ) {
        return $this->getActiveFavoredContentsWithoutCache($search, $contentSetTitleSearch);
    }

    public function getActiveFavoredContentsWithoutCache(string $search = null, string $contentSetTitleSearch = null)
    {
        return $this->favoredContents()
            ->active()
            ->search($search)
            ->whereHas('set', function ($query) use ($contentSetTitleSearch) {
                if (!is_null($contentSetTitleSearch)) {
                    return $query->search($contentSetTitleSearch);
                }
            })
            ->notRedirected()
            ->get()
            ->sortBy('pivot.created_at');
    }

    public function getActiveFavoredTimepointsForContent($contentId)
    {
        return $this->favoredTimepoints
            ->where('content_id', $contentId)
            ->sortBy('pivot.created_at');
    }

    public function getActiveFavoredProducts()
    {
        return Cache::tags([
            'favorite', 'user', 'user_'.$this->id, 'user_'.$this->id.'_favorites', 'user_'.$this->id.'_favoriteProducts'
        ])
            ->remember('user:favorite:products:'.$this->cacheKey(), config('constants.CACHE_10'), function () {
                return $this->favoredProducts()
                    ->active()
                    ->get()
                    ->sortByDesc('pivot.created_at');
            });
    }

    public function favoredProducts()
    {
        return $this->morphedByMany(Product::class, 'favorable')->withTimestamps();
    }

    public function getActiveFavoredSets()
    {
        return Cache::tags([
            'favorite', 'user', 'user_'.$this->id, 'user_'.$this->id.'_favorites', 'user_'.$this->id.'_favoriteSets'
        ])
            ->remember('user:favorite:sets:'.$this->cacheKey(), config('constants.CACHE_10'), function () {
                return $this->favoredSets()
                    ->active()
                    ->notRedirected()
                    ->get()
                    ->sortByDesc('pivot.created_at');
            });
    }

    public function favoredSets()
    {
        return $this->morphedByMany(Contentset::class, 'favorable')->withTimestamps();
    }
}
