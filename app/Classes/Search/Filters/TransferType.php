<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SmsTransferType
 * @package App\Classes\Search\Filters
 */
class TransferType extends FilterAbstract
{
    protected $attribute = 'sent';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
