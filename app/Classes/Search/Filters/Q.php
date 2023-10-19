<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Q extends FilterAbstract
{
    protected $attribute = 'q';

    /**
     * @param  Builder  $builder
     * @param                  $value
     * @param  FilterCallback  $callback
     *
     * @return Builder
     */
    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        $model = $builder->getModel();
        if (!method_exists($model, 'bootSearchable')) {
            return $builder;
        }
        $value = $this->getSearchValue($value);

        $ids = $this->getResultFromScout($value, $model);

        return $builder->whereIn('id', $ids);
    }

    /**
     * @param $value
     * @param $model
     *
     * @return mixed
     */
    private function getResultFromScout($value, $model)
    {
        return Cache::remember(md5($value), config('constants.CACHE_600'), function () use ($value, $model) {
            $ids = $model::search($value)
                ->get()
                ->pluck('id')
                ->toArray();
            return $ids;
        });
    }
}
