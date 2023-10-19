<?php

namespace App\Repositories;

use App\Models\Content;
use App\Traits\CharacterCommon;
use App\Traits\Content\ContentControllerResponseTrait;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;

class ContentRepository extends AlaaRepo
{
    use CharacterCommon;
    use ContentControllerResponseTrait;

    /**
     * @param  int  $userId
     *
     * @return Builder
     */
    public static function getContentsetByUserId(int $userId)
    {
        return Content::select('educationalcontents.contentset_id')
            ->where('author_id', $userId)
            ->where('isFree', 0)
            ->groupby('contentset_id');
    }

    public static function filter(array $filters): EloquentBuilder
    {
        $query = (new Content())->newQuery();
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }
        return $query;
    }

    public static function update(Content $content, $data)
    {
        (new ContentRepository())->fillContentFromRequest($data, $content);
        return $content->update();
    }

    public static function whereNotNullThumbnail()
    {
        return self::initiateQuery()->whereNotNull('thumbnail');
    }

    public static function initiateQuery()
    {
        $model = self::getModelClass();

        return $model::query();
    }

    public static function getModelClass(): string
    {
        return Content::class;
    }

    public static function getContentById($contentId): Content
    {
        $key = 'content:'.$contentId;
        return Cache::tags(['content', 'content_'.$contentId])
            ->remember($key, config('constants.CACHE_600'), function () use ($contentId) {
                return Content::find($contentId) ?: new Content();
            });
    }
}
