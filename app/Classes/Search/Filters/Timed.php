<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Timed extends FilterAbstract
{
    protected $attribute = 'validSince';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $value ? $builder->whereNotNull($this->attribute)->where($this->attribute, '>',
            now()->toDateTimeString()) : $builder;
    }
}
