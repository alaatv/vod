<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/25/2018
 * Time: 5:25 PM
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class DoesntHaveOrder extends FilterAbstract
{
    protected $attribute = '';

    protected $relation = 'orders';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereDoesntHave($this->relation);
    }
}
