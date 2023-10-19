<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Active extends FilterAbstract
{
    protected $attribute = 'active()';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->active();
    }
}
