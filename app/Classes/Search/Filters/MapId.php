<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class MapId extends FilterAbstract
{
    protected $attribute = 'map_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
