<?php

namespace App\Classes\Search;

use App\Classes\Search\{Filters\Tags, Tag\SlideshowTagManagerViaApi};
use App\Models\Slideshow;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

class SlideshowSearch extends SearchAbstract
{
    protected $model = Slideshow::class;

    protected $pageName = 'slideshowPage';

    protected $validFilters = [
        'title',
        'block_type_ids',
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

        return Cache::tags(['slideshow', 'slideshow_search', 'search'])
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
        $result = $query
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
            $decorator->setTagManager(new SlideshowTagManagerViaApi());
        }

        return $decorator;
    }
}
