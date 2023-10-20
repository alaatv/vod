<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Id extends FilterAbstract
{
    protected $attribute = 'id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        $value = $this->getSearchValue($value);

        return $builder->where($this->attribute, $value);
    }
}
