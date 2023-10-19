<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Coupon extends FilterAbstract
{
    protected $attribute = 'coupon_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereIn($this->attribute, $value);
    }
}
