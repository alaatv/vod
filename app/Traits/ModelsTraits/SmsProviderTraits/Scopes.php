<?php

namespace App\Traits\ModelsTraits\SmsProviderTraits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait Scopes
{
    public function scopeEnable(Builder $builder, bool $value): Builder
    {
        return $builder->where('enable', '=', $value);
    }

    public function scopeDisable(Builder $builder): Builder
    {
        return $builder->where('enable', '=', 0);
    }

    public function scopeNotDefault(Builder $builder): Builder
    {
        return $builder->where('is_default', '=', false);
    }

    public function scopeDefaults(Builder $builder, bool $value): Builder
    {
        return $builder->where('is_default', '=', $value);
    }

    public function scopeWithNumber(Builder $builder, null|string $number): Builder
    {
        return $builder->where('number', 'like', "%$number%");
    }

    public function scopeFilter(Builder $builder, array $filters):Builder
    {

        if($operator = Arr::get($filters, 'operator_id'))
        {
            $builder->where('operator_id', $operator);
        }

        if($number = Arr::get($filters, 'number'))
        {
            $builder->withNumber($number);
        }

        if(Arr::has($filters, 'enable'))
        {
            $builder->enable(Arr::get($filters, 'enable'));
        }

        if(Arr::get($filters, 'disable'))
        {
            $builder->disable();
        }

        if(Arr::has($filters, 'defaults'))
        {
            $builder->defaults(Arr::get($filters, 'defaults'));
        }

        return $builder;
    }

}
