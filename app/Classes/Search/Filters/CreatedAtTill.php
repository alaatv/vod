<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/25/2018
 * Time: 5:23 PM
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class CreatedAtTill extends FilterAbstract
{
    protected $attribute = 'created_at';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if (!isset($value)) {
            return $builder;
        }

        return $builder->where($this->attribute, '<=', $value);
    }
}
