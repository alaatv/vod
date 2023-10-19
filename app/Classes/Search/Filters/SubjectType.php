<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class SubjectType extends FilterAbstract
{
    protected $attribute = 'subject_type';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        $value = str_replace('App\\\\', '', $value);
        $value = str_replace('App\\', '', $value);
        return parent::apply($builder, $value, $callback);
    }
}
