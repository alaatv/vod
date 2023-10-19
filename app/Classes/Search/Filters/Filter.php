<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:15
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

interface Filter
{
    /**
     * Apply a given search value to the builder instance.
     *
     * @param  Builder  $builder
     * @param  mixed  $value
     * @param  FilterCallback  $callback
     *
     * @return Builder $builder
     */
    public function apply(Builder $builder, $value, FilterCallback $callback): Builder;
}
