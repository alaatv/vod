<?php

namespace App\Classes\Search;

use App\Models\Eventresult;
use Illuminate\{Contracts\Pagination\LengthAwarePaginator, Database\Eloquent\Builder, Support\Facades\Cache};

class EventResultSearch extends SearchAbstract
{
    protected $model = Eventresult::class;

    protected $pageName = 'eventResultPage';

    protected $validFilters = [
        'event_id',
        'user_id',
    ];

    protected function apply(array $filters): LengthAwarePaginator
    {
        $this->pageNum = $this->setPageNum($filters);
        $key = $this->makeCacheKey($filters);

        return Cache::tags(['event_result', 'event_result_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());

                return $this->getResults($query);
            });
    }

    protected function getResults(Builder $query): LengthAwarePaginator
    {
        return $query->orderBy('created_at', 'desc')
            ->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum);
    }

    /**
     * @param $decorator
     *
     * @return mixed
     */
    protected function setupDecorator($decorator): mixed
    {
        return new $decorator();
    }
}
