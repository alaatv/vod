<?php

namespace App\Classes\Search;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

class TransactionSearch extends SearchAbstract
{
    protected $model = Transaction::class;

    protected $pageName = 'transactionPage';

    protected $numberOfItemInEachPage = 10;

    protected $validFilters = [
        // TODO: The transaction table doesn't have any of the following fields!!!
        //  I think the transaction table no needed to any search item.
        'cost',
        'authority',
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
     * @param  Builder|Transaction  $query
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
