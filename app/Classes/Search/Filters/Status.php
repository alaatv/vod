<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SmsUserStatus
 * @package App\Classes\Search\Filters
 */
class Status extends FilterAbstract
{
    protected $attribute = 'status';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if ($value === 0 || $value === '0') {
            return $builder->whereNull($this->attribute);
        }

        return $builder->where($this->attribute, $value);
    }
}
