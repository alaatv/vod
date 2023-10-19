<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class CauserIds extends FilterAbstract
{
    protected $attribute = 'causer_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereIn($this->attribute, $value);
    }
}
