<?php

namespace App\Classes\Search;

use App\Models\Shahr;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

/**
 * Class ShahrSearch
 * @package App\Classes\Search
 */
class ShahrSearch extends SearchAbstract
{
    protected $model = Shahr::class;

    protected $pageName = 'shahrPage';

    protected $numberOfItemInEachPage = 10;

    protected $noPagination = false;

    protected $validFilters = [
        'ostan_id',
    ];

    /**
     * @param  array  $filters
     *
     * @return mixed
     */
    protected function apply(array $filters)
    {
        $this->noPagination = $this->setNoPagination($filters);
        $this->pageNum = $this->setPageNum($filters);
        $key = $this->makeCacheKey($filters);

        return Cache::tags(['shahr', 'shahr_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());

                return $this->getResults($query);
            });
    }

    /**
     * @param  Builder  $query
     *
     * @return mixed
     */
    protected function getResults(Builder $query)
    {
        $result = $query->with(['ostan'])
            ->orderBy('name');

        $result = $this->noPagination
            ? $result->get()
            : $result->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum);

        return $result;
    }

    /**
     * @param $decorator
     *
     * @return mixed
     */
    protected function setupDecorator($decorator)
    {
        return new $decorator();
    }
}
