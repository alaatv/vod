<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class HasReported extends FilterAbstract
{
    protected $attribute = 'has_reported';

    protected $relation = 'messages';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereHas($this->relation, function ($q) use ($value) {
            $q->where($this->attribute, $value);
        });
    }
}
