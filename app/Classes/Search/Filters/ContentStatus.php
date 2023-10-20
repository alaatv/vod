<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-16
 * Time: 12:40
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class ContentStatus extends FilterAbstract
{
    protected $attribute = 'content_status_id';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereIn($this->attribute, $value);
    }

}
