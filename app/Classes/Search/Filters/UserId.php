<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserId extends FilterAbstract
{
    protected $attribute = 'user_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        $value = $this->getSearchValue($value);

        return $builder->where($this->attribute, $value);
    }
}
