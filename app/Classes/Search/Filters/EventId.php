<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class EventId extends FilterAbstract
{
    protected $attribute = 'event_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
