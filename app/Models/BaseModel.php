<?php

namespace App\Models;

use App\Classes\EloquentBuilderWithCache;
use App\Traits\CharacterCommon;
use App\Traits\DateTrait;
use App\Traits\Helper;
use DateTimeInterface;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

abstract class BaseModel extends Model
{
    use SoftDeletes;
    use CascadeSoftDeletes;
    use Helper;
    use DateTrait;
    use CharacterCommon;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function getTableName()
    {
        return with(new static())->getTable();

    }

    protected $cachedMethods = [

    ];

    public function cacheKey()
    {
        $key = $this->getKey();
        $time = (optional($this->updated_at)->timestamp ?: optional($this->created_at)->timestamp) ?: 0;

        return sprintf('%s:%s-%s', $this->getTable(), $key, $time);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function attacheCachedMethodResult()
    {
        $methods = $this->getCachedMethods();
        foreach ($methods as $method) {
            $this->$method();
        }
        return $this;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     *
     * @return EloquentBuilderWithCache
     */
    public function newEloquentBuilder($query)
    {
        return new EloquentBuilderWithCache($query); // TODO: Change the autogenerated stub
    }

    public function getCachedMethods(): array
    {
        return $this->cachedMethods;
    }

    public static function getBindingCacheKey($ids): string
    {
        $ids = $ids instanceof Arrayable ? $ids->toArray() : (is_array($ids) ? $ids : [$ids]);
        $key = get_called_class().':'.implode(',', $ids);
        return $key;
    }

    public static function getBindingCacheTagArray($ids): array
    {
        $ids = $ids instanceof Arrayable ? $ids->toArray() : (is_array($ids) ? $ids : [$ids]);

        $getCalledClass = get_called_class();
        $key = strtolower(substr($getCalledClass, strrpos($getCalledClass, '\\') + 1));
        $tags = [
            $key,
        ];
        foreach ($ids as $i) {
            $tags[] = $key.'_'.$i;
        }

        return $tags;
    }
}
