<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SmsFrom
 * @package App\Classes\Search\Filters
 */
class FromMobile extends FilterAbstract
{
    protected $attribute = 'from';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if (!isset($value)) {
            return $builder;
        }

        $value = baseTelNo($value);
        return parent::apply($builder, $value, $callback);
    }
}
