<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Ids extends FilterAbstract
{
    protected $attribute = 'id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        $value = $this->getSearchValue($value);
        return $builder->whereIn($this->attribute, $value)->limit(config('constants.MAXIMUM_BULK_PRODUCTS_GET_BY_IDS'));
    }
}
