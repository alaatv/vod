<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SmsMobile
 * @package App\Classes\Search\Filters
 */
class ToMobile extends FilterAbstract
{
    protected $attribute = 'mobile';
    protected $relation = 'users';
    protected $relationTable = 'sms_user';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if (!isset($value)) {
            return $builder;
        }

        return $builder->whereHas($this->relation, function ($q) use ($value) {
            $value = baseTelNo($value);
            return $q->where($this->attribute, 'like', "%{$value}%");
        });
    }
}
