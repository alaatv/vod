<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:30
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class HasSeenSitePage extends FilterAbstract
{
    protected $attribute = 'hasseensitepage';

    protected $relation = 'seensitepages';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereHas($this->relation, function ($q) use ($value) {
            $q->whereIn('url', $value);
        });
    }
}
