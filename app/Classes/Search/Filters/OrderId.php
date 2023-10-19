<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class OrderId extends FilterAbstract
{
    protected $attribute = 'order_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
