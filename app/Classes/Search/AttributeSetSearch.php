<?php

namespace App\Classes\Search;

use App\Models\Attributeset;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

class AttributeSetSearch extends SearchAbstract
{
    protected $model = Attributeset::class;

    protected $pageName = 'attributeSetPage';

    protected $numberOfItemInEachPage = 10;

    protected $validFilters = [
        'name',
        'description',
        'order',
    ];

    /**
     * @param  array  $filters
     *
     * @return mixed
     */
    protected function apply(array $filters)
    {
        $this->pageNum = $this->setPageNum($filters);
        $key = $this->makeCacheKey($filters);

        return Cache::tags(['attribute_set', 'attribute_set_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());
                return $this->getResults($query)
                    ->appends($filters);
            });
    }

    /**
     * @param  Builder|Attributeset  $query
     *
     * @return mixed
     */
    protected function getResults(Builder $query)
    {
        $result = $query->orderBy('created_at', 'desc')
            ->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum);

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
