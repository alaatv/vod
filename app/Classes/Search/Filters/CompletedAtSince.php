<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/25/2018
 * Time: 5:25 PM
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class CompletedAtSince extends FilterAbstract
{
    protected $attribute = 'completed_at';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, '>=', $value);
    }
}
