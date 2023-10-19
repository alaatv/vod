<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;


use Illuminate\Database\Eloquent\Builder;

class Display extends FilterAbstract
{
    public const DISPLAY_IGNORE_VALUE = 2;
    protected $attribute = 'display';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if ($value == self::DISPLAY_IGNORE_VALUE) {
            return $builder;
        }
        return $builder->where($this->attribute, $value);
    }
}
