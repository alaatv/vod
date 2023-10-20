<?php

namespace App\Classes\Search;

use App\Models\SMS;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

/**
 * Class SmsSearch
 * @package App\Classes\Search
 */
class SmsSearch extends SearchAbstract
{
    protected $model = SMS::class;

    protected $pageName = 'smsPage';

    protected $numberOfItemInEachPage = 10;

    protected $validFilters = [
        'provider_ids',
        'from_mobile',
        'to_mobile',
        'transfer_type',
        'send_type',
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
        $this->fromOrTo($filters);

        return Cache::tags(['sms', 'sms_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model::query());

                return $this->getResults($query)
                    ->appends($filters);
            });
    }

    protected function fromOrTo(&$filters)
    {
        if (!isset($filters['sms_from_user_id_or_to_user_id'])) {
            $filters['sms_from_user_id_or_to_user_id'] = $fromOrTo ?? null;
            return;
        }
        $fromOrTo = null;
        if (isset($filters['sms_from_user_id'])) {
            $fromOrTo = $filters['sms_from_user_id'];
        }
        if (isset($filters['sms_user_user_id'])) {
            $fromOrTo = $filters['sms_user_user_id'];
        }

        $filters['sms_from_user_id_or_to_user_id'] = $fromOrTo ?? null;
    }

    /**
     * @param  Builder  $query
     *
     * @return mixed
     */
    protected function getResults(Builder $query)
    {
        /** @var SMS $query */
        $result = $query->with([
            'users', 'users.user', 'fromUser', 'provider', 'details', 'details.result', 'details.admin'
        ])
            ->orderBy('created_at', 'desc');

        $result = $result->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum);

        $result->each(function ($item) {
            return $item->append('edit_user_link');
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
