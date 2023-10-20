<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class DepartmentId extends FilterAbstract
{
    protected $attribute = 'department_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereIn($this->attribute, $value);
    }
}
