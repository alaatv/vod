<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Cost extends FilterAbstract
{
    protected $attribute = 'cost';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
