<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class EntityType extends FilterAbstract
{
    protected $attribute = 'entity_type';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, "App\\".$value);
    }
}
