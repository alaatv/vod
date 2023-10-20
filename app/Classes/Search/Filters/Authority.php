<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Authority extends FilterAbstract
{
    protected $attribute = 'authority';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
