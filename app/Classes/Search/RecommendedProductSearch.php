<?php


namespace App\Classes\Search;


use App\Classes\Search\Filters\Tags;
use App\Classes\Search\Tag\RecommendedProductTagManagerViaApi;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class RecommendedProductSearch extends SearchAbstract
{
    protected $model = Product::class;

    protected $pageName = 'recommendedProductPage';

    protected $numberOfItemInEachPage = 10;

    protected $validFilters = [
        'tags',
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

        return Cache::tags(['recommendedProduct', 'recommendedProduct_search', 'search'])->remember($key,
            $this->cacheTime, function () use ($filters) {
                $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());

                return $this->getResults($query);
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
            ->active()
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate($this->numberOfItemInEachPage, ['*'],
                $this->pageName, $this->pageNum);

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
            $decorator->setTagManager(new RecommendedProductTagManagerViaApi());
        }

        return $decorator;
    }
}
