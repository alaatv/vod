<?php


namespace App\Repositories;

use App\Models\Grade;

class GradeRepo
{
    public static function getBasicGrades()
    {
        return Grade::whereIn('id', [1, 2, 5, 6, 7, 8, 9, 10])->orderBy('order');
    }

    public static function get3AGrades()
    {
        return Grade::whereIn('id', [1, 2, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14])->orderBy('order');
    }
}
