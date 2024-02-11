<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class EloquentBuilderWithCache extends Builder
{
    public function findOrFail($id, $columns = ['*'])
    {
        $modelCacheKey = get_class($this->model)::getBindingCacheKey($id);
        $modelCacheTagArray = get_class($this->model)::getBindingCacheTagArray($id);

        return Cache::tags($modelCacheTagArray)->remember($modelCacheKey, config('constants.CACHE_5'),
            callback: function () use ($id, $columns) {
                $object = parent::findOrFail($id, $columns);
                return $this->attacheCachedMethodResult($object);
            });
    }
    private function attacheCachedMethodResult ( $object )
    {
        if( isset($object ) and method_exists($object, 'attacheCachedMethodResult') ) {
            $object?->attacheCachedMethodResult();
        }
        return $object;
    }

    public function find($id, $columns = ['*'])
    {
        $modelCacheKey = get_class($this->model)::getBindingCacheKey($id);
        $modelCacheTagArray = get_class($this->model)::getBindingCacheTagArray($id);

        return Cache::tags($modelCacheTagArray)->remember($modelCacheKey, config('constants.CACHE_10'),
            function () use ($id, $columns) {
                $object = parent::find($id, $columns);
                return $this->attacheCachedMethodResult($object);
            });
    }

    public function findMany($ids, $columns = ['*'])
    {
        $modelCacheKey = get_class($this->model)::getBindingCacheKey($ids);
        $modelCacheTagArray = get_class($this->model)::getBindingCacheTagArray($ids);

        return Cache::tags($modelCacheTagArray)->remember($modelCacheKey, config('constants.CACHE_10'),
            function () use ($ids, $columns) {
                $objects = parent::findMany($ids, $columns);

                return $objects?->map(function ($object) {
                    return $this->attacheCachedMethodResult($object);
                });
            });
    }
}
