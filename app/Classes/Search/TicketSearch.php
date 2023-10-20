<?php


namespace App\Classes\Search;

use App\Classes\Search\{Filters\Tags, Tag\TicketTagManagerViaApi};
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class TicketSearch extends SearchAbstract
{

    protected $model = Ticket::class;

    protected $pageName = 'ticketPage';

    protected $validFilters = [
        'id',
        'title',
        'user_id',
        'department_id',
        'priority_id',
        'status_id',
        'order_id',
        'orderproduct_id',
        'tags',
        'has_user_mobile',
        'has_user_nationalcode',
        'has_user_firstname',
        'has_user_lastname',
        'created_at_since',
        'created_at_till',
        'ticketMessage',
        'hasAssignees',
        'hasReported'
    ];

    /**
     * @param  array  $filters
     *
     * @return mixed
     */
    protected function apply(array $filters): LengthAwarePaginator
    {
        $this->pageNum = $this->setPageNum($filters);
        $key = $this->makeCacheKey($filters);

        return Cache::tags(['ticket', 'ticket_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());
                return $this->getResults($query)
                    ->appends($filters);
            });
    }

    protected function getResults(Builder $query)
    {
        $result = $query
            ->orderBy('status_id')
            ->orderBy('created_at', 'desc')
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
        $decorator = new $decorator();
        if ($decorator instanceof Tags) {
            $decorator->setTagManager(new TicketTagManagerViaApi());
        }

        return $decorator;
    }
}
