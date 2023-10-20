<?php


namespace App\Repositories;


use App\Models\Major;

class MajorRepo
{
    public static function getBasicMajors()
    {
        return Major::enable();
    }
}
