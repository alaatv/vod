<?php
/**
 * Created by PhpStorm.
 * User: amir_pou
 * Date: 29/9/2020
 * Time: 13:13 PM
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class DoesNotHavePinned extends FilterAbstract
{
    protected $attribute = 'pinned_at';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->where($this->attribute, null);
    }
}
