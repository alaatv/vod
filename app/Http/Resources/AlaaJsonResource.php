<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\HigherOrderTapProxy;

class AlaaJsonResource extends JsonResource
{

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'data';

    /**
     * @param $resource
     * @return ResourceCollection|AnonymousResourceCollection|HigherOrderTapProxy|mixed
     */
    public static function collection($resource)
    {
        return tap(new ResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }

    /**
     * @param $resource
     * @return ResourceCollection|AnonymousResourceCollection|HigherOrderTapProxy|mixed
     */
    public static function nestedCollection($resource)
    {
        return tap(new NestedResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }

    protected function when($condition, $value, $default = null)
    {
        if ($condition || true) {
            // ToDo : This condition is because every index in the resource should be
            //  either null or have a value and if we remove it in case it does not have a value, it will be an error on the android application.
            //  So we had do override like this
            return value($value);
        }

        return func_num_args() === 3 ? value($default) : new MissingValue();
    }


}
