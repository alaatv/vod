<?php

namespace App\Classes;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class EloquentBuilderWithCache extends Builder
{
    public function findOrFail($id, $columns = ['*'])
    {
        $modelCacheKey = get_class($this->model)::getBindingCacheKey($id);
        $modelCacheTagArray = get_class($this->model)::getBindingCacheTagArray($id);

        return Cache::tags($modelCacheTagArray)->remember($modelCacheKey, config('constants.CACHE_5'), function () use ($id, $columns) {
            $object = parent::findOrFail($id, $columns);
            $object?->attacheCachedMethodResult();
            return $object;
        });
    }


    public function find($id, $columns = ['*'])
    {
        $modelCacheKey = get_class($this->model)::getBindingCacheKey($id);
        $modelCacheTagArray = get_class($this->model)::getBindingCacheTagArray($id);

        return Cache::tags($modelCacheTagArray)->remember($modelCacheKey, config('constants.CACHE_10'), function () use ($id, $columns) {
            $object =  parent::find($id, $columns);
            $object?->attacheCachedMethodResult();
            return $object;
        });
    }

    public function findMany($ids, $columns = ['*'])
    {
        $modelCacheKey = get_class($this->model)::getBindingCacheKey($ids);
        $modelCacheTagArray = get_class($this->model)::getBindingCacheTagArray($ids);
        return Cache::tags($modelCacheTagArray)->remember($modelCacheKey, config('constants.CACHE_10'), function () use ($ids, $columns) {
            $objects =  parent::findMany($ids, $columns);
            return $objects?->map(function ($object){
                return $object->attacheCachedMethodResult();
            });
        });
    }
}
