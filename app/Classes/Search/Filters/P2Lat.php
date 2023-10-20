<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class P2Lat extends FilterAbstract
{
    protected $attribute = 'lat';

    protected $relation = 'latlngs';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereHas($this->relation, function ($q) use ($value) {
            $q->where($this->attribute, '<', $value);
        });
    }
}
