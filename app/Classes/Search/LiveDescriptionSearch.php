<?php

namespace App\Classes\Search;

use App\Classes\Search\Filters\Tags;
use App\Classes\Search\Tag\LiveDescriptionTagManagerViaApi;
use App\Models\LiveDescription;
use App\Models\LiveDescription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class LiveDescriptionSearch extends SearchAbstract
{
    protected $model = LiveDescription::class;

    protected $pageName = 'liveDescriptionPage';

    protected $numberOfItemInEachPage = 5;

    protected $validFilters = [
        'tags',
        'entity_id',
        'entity_type',
        'does_not_have_pinned',
        'created_at_since',
        'entity_ids',
        'owner'
    ];

    protected function apply(array $filters)
    {
        $this->pageNum = $this->setPageNum($filters);
        $key = $this->makeCacheKey($filters);
        return Cache::tags(['live_description', 'live_description_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());
                $query = $this->sort($query, $filters);
                return $this->getResults($query);
            });
    }

    protected function getResults(Builder $query)
    {
        return $query->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum)->appends($_GET);
    }

    protected function setupDecorator($decorator)
    {
        $decorator = (new $decorator());
        if ($decorator instanceof Tags) {
            $decorator->setTagManager(new LiveDescriptionTagManagerViaApi());
        }
        return $decorator;
    }


}
