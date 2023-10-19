<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SmsUserSmsId
 * @package App\Classes\Search\Filters
 */
class SmsId extends FilterAbstract
{
    protected $attribute = 'sms_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
