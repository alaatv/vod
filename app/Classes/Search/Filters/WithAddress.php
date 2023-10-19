<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class WithAddress extends FilterAbstract
{
    protected $attribute = 'address';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where(function ($q) {
            $q->whereNotNull($this->attribute)
                ->Where($this->attribute, '<>', '');
        });
    }
}
