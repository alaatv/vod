<?php

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class BlockTypeIds extends FilterAbstract
{
    protected $attribute = 'type';
    protected $relation = 'blocks';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if (!(!is_null($value) && count($value))) {

            return $builder;
        }

        $selectedBlockTypes = $value;
        if (in_array(0, $selectedBlockTypes)) {
            return $builder->where(function ($q) use ($selectedBlockTypes) {
                return $q->whereDoesntHave($this->relation)
                    ->orWhereHas($this->relation, function ($q) use ($selectedBlockTypes) {
                        return $q->whereIn($this->attribute, $selectedBlockTypes);
                    });
            });
        }

        return $builder->whereHas($this->relation, function ($q) use ($selectedBlockTypes) {
            return $q->whereIn($this->attribute, $selectedBlockTypes);
        });
    }
}
