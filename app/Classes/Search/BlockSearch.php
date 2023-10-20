<?php

namespace App\Classes\Search;

use App\Classes\Search\{Filters\Tags, Tag\BlockTagManagerViaApi};
use App\Models\Block;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

/**
 * Class BlockSearch
 * @package App\Classes\Search
 */
class BlockSearch extends SearchAbstract
{
    protected $model = Block::class;

    protected $pageName = 'blockPage';

    protected $numberOfItemInEachPage = 10;

    protected $validFilters = [
        'type',
        'title',
        'customUrl',
        'class_field',
        'enable',

//        'tags',

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

        return Cache::tags(['block', 'block_search', 'search'])
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
        /** @var Block $query */
        $result = $query
            ->orderBy('created_at', 'desc')
            ->with('blockType')
            ->withCount('products')
            ->withCount('sets')
            ->withCount('contents')
            ->withCount('banners');
        // TODO: I searched a bit but did not understand why the following code does not work.
        //  To make it work temporarily, I used the $append attribute on block model instead of the code below.
//        $result->each(function ($items) {
//            $items->append('edit_link');
//            $items->append('remove_link');
//        });
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
        $decorator = (new $decorator());
        if ($decorator instanceof Tags) {
            $decorator->setTagManager(new BlockTagManagerViaApi());
        }

        return $decorator;
    }
}
