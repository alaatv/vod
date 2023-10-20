<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SmsSendType
 * @package App\Classes\Search\Filters
 */
class SendType extends FilterAbstract
{
    protected $attribute = 'pattern_code';
    protected $relation = 'details';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->sent()
            ->whereHas($this->relation, function ($q) use ($value) {
                if ($value === 0 || $value === '0') {
                    return $q->whereNotNull($this->attribute);
                }
                return $q->whereNull($this->attribute);
            });
    }
}
