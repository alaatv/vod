<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class SubjectId extends FilterAbstract
{
    protected $attribute = 'subject_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
