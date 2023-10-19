<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Mobile extends FilterAbstract
{
    protected $attribute = 'mobile';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if (!isset($value)) {
            return $builder;
        }

        $value = baseTelNo($value);
        return parent::apply($builder, $value, $callback);
    }
}
