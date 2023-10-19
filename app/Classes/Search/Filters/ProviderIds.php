<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SmsProviderIds
 * @package App\Classes\Search\Filters
 */
class ProviderIds extends FilterAbstract
{
    protected $attribute = 'provider_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereIn($this->attribute, $value);
    }
}
