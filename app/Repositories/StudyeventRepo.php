<?php

namespace App\Repositories;

use App\Models\Studyevent;

class StudyeventRepo
{
    public static function findStudyeventByName(string $name)
    {
        return Studyevent::where('name', $name)->first();
    }
}
