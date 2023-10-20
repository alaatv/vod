<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Zoom extends FilterAbstract
{
    protected array $attributes = ['min_zoom', 'max_zoom'];


    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attributes[0], '<=', $value)->where($this->attributes[1], '>=', $value);
    }
}
