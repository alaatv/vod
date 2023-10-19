<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;
use LogicException;

class PublishAt extends FilterAbstract
{
    protected $attribute = 'validSince';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        throw new LogicException('implements publishAt');
    }
}
