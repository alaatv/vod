<?php

namespace App\Classes;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Contentset;
use App\Models\Product;
use App\Models\User;
use App\Models\WatchHistory;
use App\Models\WatchHistory;

class LastWatch
{
    public function __construct(
        public User $user,
        public string $watchableType,
        public int|array $watchableId,
        public ?int $studyEventId = null
    ) {
    }

    private function set($watchableId)
    {
        $contentIds = Content::where('contentset_id', $watchableId)->active()
            ->where('contenttype_id', Content::CONTENT_TYPE_VIDEO)
            ->orderBy('order')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();
        return $this->getLastContentUserWatched($contentIds);
    }

    private function getLastContentUserWatched($contentIds)
    {
        $watchedContentIds = WatchHistory::watchableType('content')
            ->whereIn('watchable_id', $contentIds)
            ->where('user_id', $this->user->id)
            ->get()
            ->pluck('watchable_id')
            ->toArray();

        if (!isset($contentIds[0])) {
            return null;
        }
        // Return first product's content if user hasn't watched any of them.
        if (empty($watchedContentIds)) {
            return Content::find($contentIds[0]);
        }
        $orderedWatchedContentIds = array_intersect($contentIds, $watchedContentIds);
        $lastWatchedContentId = end($orderedWatchedContentIds);
        return Content::find($lastWatchedContentId);
    }

    public function get()
    {
        return $this->{$this->watchableType}($this->watchableId);
    }

    private function product($watchableIds)
    {
        $products = Product::whereIn('id', $watchableIds)
            ->whereRelation('sets', function ($query) {
                return $query->nameDoesNotContain(Contentset::NEXT_WATCH_CONTENT_NOT_CONTAIN_CONTENT_SET_STRINGS)
                    ->smallNameDoesNotContain(Contentset::NEXT_WATCH_CONTENT_NOT_CONTAIN_CONTENT_SET_STRINGS)
                    ->whereRelation('activeContents', function ($q) {
                        $q->where('contenttype_id', Content::CONTENT_TYPE_VIDEO);
                    });
            })
            ->with([
                'sets' => function ($query) {
                    $query->nameDoesNotContain(Contentset::NEXT_WATCH_CONTENT_NOT_CONTAIN_CONTENT_SET_STRINGS)
                        ->smallNameDoesNotContain(Contentset::NEXT_WATCH_CONTENT_NOT_CONTAIN_CONTENT_SET_STRINGS)
                        ->orderBy('pivot_order')
                        ->orderBy('id');
                },
                'sets.activeContents' => function ($query) {
                    $query->where('contenttype_id', Content::CONTENT_TYPE_VIDEO)
                        ->orderBy('order')
                        ->orderBy('id');
                }
            ])->get();
        foreach ($products as $product) {
            $contentIds = [];
            foreach ($product->getRelations()['sets'] as $set) {
                foreach ($set->activeContents as $content) {
                    $contentIds[] = $content->id;
                }
            }
            $product->last_watch_content = $this->getLastContentUserWatched($contentIds);
            $contOfUserWatched = $this->user->watchContents()->when(isset($this->studyEventId), function ($query) {
                $query->where('studyevent_id', $this->studyEventId);
            })
                ->whereHas('set', function ($query) use ($product) {
                    $query->whereHas('products', function ($query) use ($product) {
                        $query->whereId($product->id);
                    });
                })->get()->count();
            $product->contents_progress =
                (count($contentIds)) ? (int) round(($contOfUserWatched / count($contentIds)) * 100) : 0;

        }
        return $products;
    }
}
