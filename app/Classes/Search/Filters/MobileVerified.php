<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class MobileVerified extends FilterAbstract
{
    protected $attribute = 'mobile_verified_at';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereNotNull($this->attribute);
    }
}
