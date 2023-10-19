<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class BlockType
 * @package App\Classes\Search\Filters
 */
class Type extends FilterAbstract
{
    protected $attribute = 'type';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereIn($this->attribute, $value);
    }
}
