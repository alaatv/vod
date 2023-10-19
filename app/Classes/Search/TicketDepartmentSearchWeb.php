<?php


namespace App\Classes\Search;

use App\Classes\Search\{Filters\Tags, Tag\TicketDepartmentTagManagerViaApi};
use App\Models\TicketDepartment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class TicketDepartmentSearchWeb extends SearchAbstract
{

    protected $model = TicketDepartment::class;

    protected $pageName = 'departmentPage';

    protected $validFilters = [
        //
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

        return Cache::tags(['ticketDepartment', 'ticketDepartment_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());
                return $this->getResults($query);
            });
    }

    protected function getResults(Builder $query)
    {
        $result = $query->orderBy('order');

        $result = $result->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum);

        return $result;
    }

    /**
     * @param $decorator
     *
     * @return mixed
     */
    protected function setupDecorator($decorator)
    {
        $decorator = new $decorator();
        if ($decorator instanceof Tags) {
            $decorator->setTagManager(new TicketDepartmentTagManagerViaApi());
        }

        return $decorator;
    }
}
