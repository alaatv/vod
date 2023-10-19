<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Owner extends FilterAbstract
{
    protected $attribute = 'owner';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
