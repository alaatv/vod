<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/10/2018
 * Time: 12:34 PM
 */

namespace App\Classes\Search;

use Illuminate\{Contracts\Pagination\LengthAwarePaginator, Support\Facades\Cache};

class UserSearchOnlyTrashed extends UserSearch
{

    protected function apply(array $filters): LengthAwarePaginator
    {
        $this->pageNum = $this->setPageNum($filters);
        $key = 'trashed'.$this->makeCacheKey($filters);

        return Cache::tags(['user', 'user_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->onlyTrashed()->newQuery());

                return $this->getResults($query);
            });
    }


}
