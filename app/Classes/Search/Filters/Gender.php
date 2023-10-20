<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/25/2018
 * Time: 5:25 PM
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Gender extends FilterAbstract
{
    protected $attribute = 'gender_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if ($value == 0) {
            return $builder->whereNull($this->attribute);
        }
        return $builder->where($this->attribute, $value);
    }
}
