<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class EntityIds extends FilterAbstract
{
    protected $attribute = 'entity_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereIn($this->attribute, $value);
    }
}
