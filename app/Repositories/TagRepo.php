<?php

namespace App\Repositories;

use App\Models\Tag;

class TagRepo extends AlaaRepo
{

    public static function getModelClass(): string
    {
        return Tag::class;
    }


    public static function getAllEnableTagsBuilder()
    {
        return self::initiateQuery()->enable();
    }
}
