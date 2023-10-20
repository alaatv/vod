<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class OstanId extends FilterAbstract
{
    protected $attribute = 'ostan_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
