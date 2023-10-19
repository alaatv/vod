<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-02
 * Time: 18:04
 */

namespace App\Classes\Format;

use App\Collection\SetCollection;
use App\Models\Contentset;
use App\Models\Contentset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class webSetCollectionFormatter implements SetCollectionFormatter
{
    /**
     * @param  SetCollection  $sets
     *
     * @return Collection
     */
    public function format(SetCollection $sets)
    {
        $lessons = collect();
        foreach ($sets as $set) {
            /** @var Contentset $set */
            $lessons->push($this->formatSet($set));
        }
        return $lessons;
    }

    /**
     * @param  Contentset  $set
     *
     * @return array
     */
    private function formatSet(Contentset $set): array
    {
        $key = 'set:formatSet:'.$set->id;
        return Cache::tags(['set', 'set_'.$set->id, 'set_'.$set->id.'_formatSet'])
            ->remember($key, config('constants.CACHE_60'), function () use ($set) {
                $content = $set->getLastActiveContent();
                $lesson = [
                    'displayName' => $set->shortName,
                    'author' => $content->author,
                    'pic' => $set->photo,
                    'content_id' => $content->id,
                    'content_count' => $set->active_contents_count,
                ];

                return $lesson;
            });

    }
}
