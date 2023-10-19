<?php

namespace App\Classes\Search;

use App\Models\SmsUser;
use App\Models\SmsUser;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

/**
 * Class SmsSearch
 * @package App\Classes\Search
 */
class SmsUserSearch extends SearchAbstract
{
    protected $model = SmsUser::class;

    protected $pageName = 'smsUserPage';

    protected $numberOfItemInEachPage = 10;

    protected $validFilters = [
        'sms_id',
        'mobile',
        'status',
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

        return Cache::tags(['sms_user', 'sms_user_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model::query());

                return $this->getResults($query)
                    ->appends($filters);
            });
    }

    /**
     * @param  Builder  $query
     *
     * @return mixed
     */
    protected function getResults(Builder $query)
    {
        /** @var SmsUser $query */
        $result = $query->with(['sms', 'user'])
            ->orderBy('created_at', 'desc');

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
        return new $decorator();
    }
}
