<?php

namespace App\Repositories;

use App\Models\Vast;
use App\Models\Vast;
use Illuminate\Database\Eloquent\Builder;

class VastRepo extends AlaaRepo
{
    public static function getModelClass(): string
    {
        return Vast::class;
    }

    public static function latest(): Builder
    {
        return self::initiateQuery()->latest();
    }

    public static function initiateQuery()
    {
        return parent::initiateQuery();
    }

    public static function randomDefault()
    {
        return self::defaultVasts()->inRandomOrder();
    }

    public static function defaultVasts()
    {
        return self::initiateQuery()->enable()
            ->isDefault();
    }
}
