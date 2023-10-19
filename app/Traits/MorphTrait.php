<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait MorphTrait
{
    /**
     * @param  string  $morphable
     * @return mixed
     */
    public function getResourceByModel(string $morphable): mixed
    {
        $morphableType = "{$morphable}_type";
        $modelKeyStr = 'model';
        $resourceKeyStr = 'resource';
        $defaultResourceKeyStr = 'default_resource';

        $modelInfo = nestedArraySearchWithKey(config('constants.MORPH_MAP_MODELS'), $this->$morphableType,
            $modelKeyStr);

        if (Arr::has($modelInfo, $resourceKeyStr)) {
            $resource = Arr::get($modelInfo, $resourceKeyStr);
            if (
                !is_null($resource) &&
                !empty($resource) &&
                Arr::has($resource, $morphable) &&
                !is_null($resource[$morphable]) &&
                class_exists($resource[$morphable])
            ) {
                return new $resource[$morphable]($this->$morphable);
            }
        }

        if (!Arr::has($modelInfo, $defaultResourceKeyStr)) {

            return null;
        }
        $resource = Arr::get($modelInfo, $defaultResourceKeyStr);
        if (
            !is_null($resource) &&
            !empty($resource) &&
            class_exists($resource)
        ) {
            return new $resource($this->$morphable);
        }


        return null;
    }
}
