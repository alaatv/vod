<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class City extends FilterAbstract
{
    protected $attribute = 'shahr_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, $value);
    }
}
