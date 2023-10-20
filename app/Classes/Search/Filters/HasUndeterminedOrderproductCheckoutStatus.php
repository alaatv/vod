<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/25/2018
 * Time: 5:25 PM
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class HasUndeterminedOrderproductCheckoutStatus extends FilterAbstract
{
    protected $attribute = 'checkoutstatus_id';

    protected $relation = 'closedorderproducts';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereHas($this->relation, function ($q) use ($value) {
            $q->whereNull($this->attribute, $value);
        });
    }
}
