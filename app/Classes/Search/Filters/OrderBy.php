<?php


namespace App\Classes\Search\Filters;


use Illuminate\Database\Eloquent\Builder;

class OrderBy extends FilterAbstract
{
    protected $attribute = 'orderBy';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->orderBy($value, 'desc');
    }
}
