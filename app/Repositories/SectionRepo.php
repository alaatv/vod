<?php


namespace App\Repositories;

use App\Models\Section;

class SectionRepo
{
    public static function filterContentsBySet(int $setId)
    {
        return Section::with([
            'contents' => function ($query) use ($setId) {
                $query->where('contentset_id', $setId);
            }
        ])->whereHas('contents', function ($query) use ($setId) {
            $query->where('contentset_id', $setId);
        })->orderBy('order');
    }

}
