<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class StatusId extends FilterAbstract
{
    protected $attribute = 'status_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereIn($this->attribute, $value);
    }
}
