<?php

namespace App\Repositories;

use App\Models\Websitesetting;
use App\Models\Websitesetting;
use Illuminate\Database\Eloquent\Builder;

class WebsiteSettingRepo extends AlaaRepo
{

    public static function all(): Builder
    {
        return self::initiateQuery();
    }

    public static function getModelClass(): string
    {
        $model = Websitesetting::class;
        return $model;
    }
}
