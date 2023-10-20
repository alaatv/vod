<?php

namespace App\Traits;


use App\Models\Contentset;
use App\Models\Section;

trait SetCommon
{
    /**
     * @param  Contentset  $set
     * @return array
     */
    public function contentSetVideoContentsStatistics(Contentset $set): array
    {
        $contentCount = 0;
        $contentSum = 0;
        $timepointCount = 0;
        $sectionContents = [];

        foreach ($set->contents()->video()->active()->get() as $content) {
            $contentTimepointCount = $content->timepoints->count();
            $contentCount++;
            $contentSum += $content->duration;
            $timepointCount += $contentTimepointCount;

            /** @var Section $section */
//                $sectionContents[$content->section_id]['id']                   = $section->id;
//                $sectionContents[$content->section_id]['title']                = $section->name;
//                $sectionContents[$content->section_id]['number_of_sessions']   = ($sectionContents[$content->section_id]['number_of_sessions'] ?? 0) + 1;
//                $sectionContents[$content->section_id]['total_seconds']        = ($sectionContents[$content->section_id]['total_seconds'] ?? 0) + ($content->duration ?? 0);
//                $sectionContents[$content->section_id]['number_of_timepoints'] = ($sectionContents[$content->section_id]['number_of_timepoints'] ?? 0) + $contentTimepointCount;

        }

        return [
            'id' => $set->id,
            'title' => $set->name,
            'number_of_sessions' => $contentCount,
            'total_seconds' => $contentSum,
            'number_of_timepoints' => $timepointCount,
            'sections' => array_values($sectionContents),
        ];
    }
}
