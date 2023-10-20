<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class OrderproductId extends FilterAbstract
{
    protected $attribute = 'orderproduct_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        $value = $this->getSearchValue($value);

        return $builder->where($this->attribute, $value);
    }
}
