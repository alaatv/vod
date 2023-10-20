<?php

namespace App\Classes\Search;

use App\Models\Tag;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

/**
 * Class SmsSearch
 * @package App\Classes\Search
 */
class TagSearch extends SearchAbstract
{
    protected $model = Tag::class;

    protected $pageName = 'tagPage';

    protected $numberOfItemInEachPage = 10;

    protected $validFilters = [
//        'name',
//        'tag_value',
//        'tag_group_id',
//        'tag_enable',
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

        return Cache::tags(['tag', 'tag_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());

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
        /** @var Tag $query */
        $result = $query->with(['group'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        $result = $result->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum);

        $result->each(function ($item) {
            return $item->append('edit_link');
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
