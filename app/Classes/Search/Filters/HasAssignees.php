<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class HasAssignees extends FilterAbstract
{
    protected $attribute = 'id';

    protected $relation = 'assignees';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereHas($this->relation, function ($q) use ($value) {
            $q->whereIn($this->attribute, $value);
        });
    }
}
