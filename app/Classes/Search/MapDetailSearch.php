<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-17
 * Time: 18:41
 */

namespace App\Classes\Search;

use App\Classes\Search\{Filters\Tags, Tag\MapDetailTagManagerViaApi};
use App\Models\MapDetail;
use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Support\Facades\{Cache};

class MapDetailSearch extends SearchAbstract
{
    protected $model = MapDetail::class;

    protected $pageName = 'mapDetailPage';

//    protected $numberOfItemInEachPage = 2;

    protected $validFilters = [
        'map_id',
        'type_id',
        'tags',
        'p1_lat',
        'p1_lng',
        'p2_lat',
        'p2_lng',
        'zoom',
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

        return Cache::tags(['mapDetail', 'mapDetail_search', 'search'])
            ->remember($key, $this->cacheTime, function () use ($filters) {

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
            ->orderBy('created_at', 'desc')
            ->get();

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
            $decorator->setTagManager(new MapDetailTagManagerViaApi());
        }

        return $decorator;
    }
}
