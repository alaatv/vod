<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-02
 * Time: 17:40
 */

namespace App\Classes\Format;

use App\Collection\BlockCollection;
use App\Models\Block;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class webBlockCollectionFormatter implements BlockCollectionFormatter
{

    public function __construct(
        protected SetCollectionFormatter $formatter
    ) {
    }

    public function format(BlockCollection $blocks): Collection
    {
        $sections = collect();

        $tasks = [];
        foreach ($blocks as $block) {
//            $tasks[] = (fn() => $this->blockFormatter($block));
            $tasks[] = $this->blockFormatter($block);
        }
//        $results = Octane::concurrently($tasks,config('constants.OCTANE_CONCURRENTLY_TIME_OUT'));
        $results = $tasks;

        foreach ($results as $value) {
            $sections->push($value);
        }
        return $sections;
    }

    private function blockFormatter(Block $block): array
    {
        return Cache::tags(['block', 'block_', $block->id])
            ->remember('block:formatBlock:'.$block->id, config('constants.CACHE_600'), function () use ($block) {
                return [
                    'name' => $block->class,
                    'displayName' => $block->title,
                    'descriptiveName' => $block->title,
                    'lessons' => $this->formatter->format($block->sets),
                    'tags' => $block->tags,
                    'ads' => [

                    ],
                    'class' => $block->class,
                    'url' => $block->url,
                ];
            });

    }
}
