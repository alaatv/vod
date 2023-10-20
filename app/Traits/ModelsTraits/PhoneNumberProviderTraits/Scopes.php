<?php

namespace App\Traits\ModelsTraits\PhoneNumberProviderTraits;

use Illuminate\Database\Eloquent\Builder;

trait Scopes
{
    public function scopeWhereIsDefault(Builder $builder): Builder
    {
        return $builder->where('default', '=', 1);
    }
}
