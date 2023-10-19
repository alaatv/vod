<?php

namespace App\Classes\Search;

use App\Models\User;
use Illuminate\{Database\Eloquent\Builder, Support\Facades\Cache};

class UserUpdateProvinceCitySearch extends SearchAbstract
{
    protected $model = User::class;

    protected $pageName = 'userProfileUpdateProvinceCityPage';

    protected $validFilters = [
    ];

    protected function apply(array $filters)
    {
        $this->pageNum = $this->setPageNum($filters);
        $key = $this->makeCacheKey($filters);

        return Cache::tags(['user', 'user_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());

                return $this->getResults($query)
                    ->appends($filters);
            });
    }

    protected function getResults(Builder $query)
    {
        $result = $query->with('ostan', 'shahr')->where(function ($q) {
            $q->whereNull('ostan_id')
                ->orWhereNull('shahr_id');
        })
            ->where(function ($q) {
                $q->whereNotNull('province')
                    ->orWhereNotNull('city');
            })
            ->orderBy('created_at', 'desc');

        $result = $result->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum);

        $result->each(function ($item) {
            return $item->append('provinces');
        });

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
