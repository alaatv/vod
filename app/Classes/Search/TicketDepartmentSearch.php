<?php


namespace App\Classes\Search;

use App\Classes\Search\{Filters\Tags, Tag\TicketDepartmentTagManagerViaApi};
use App\Models\TicketDepartment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TicketDepartmentSearch extends SearchAbstract
{

    protected $model = TicketDepartment::class;

    protected $pageName = 'departmentPage';

    protected $validFilters = [
        'tags'
    ];

    /**
     * @param  array  $filters
     *
     * @return mixed
     */
    protected function apply(array $filters): Collection
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
        return $query
            ->orderBy('order')
            ->get();
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
