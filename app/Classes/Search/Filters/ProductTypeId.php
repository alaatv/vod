<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductTypeId extends FilterAbstract
{
    protected $attribute = 'producttype_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
