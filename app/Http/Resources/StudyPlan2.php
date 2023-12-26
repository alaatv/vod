<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class \App\Models\Studyplan
 *
 * @mixin \App\Models\Studyplan
 * */
class StudyPlan2 extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource;
        $plans = $data->plans->groupBy('section_name');

        return [
            'studyPlan_id' => $data->id,
            'id' => $data->plan_date,
            'date' => $data->plan_date,
            'voice' => $data->voice,
            'body' => $data->body,
            'title' => $data->title,
            'plans' => $this->getPlansArray($plans),
            'contents' => null,
        ];
    }

    public function getPlansArray($plans)
    {
        $result = [];
        $a = 0;
        foreach ($plans as $plan) {
            $data = $plan->first();
            $result[$a][$this->getMajorKey($data->major)] = $this->getPlan($data);
            $data = $plan->last();
            $result[$a][$this->getMajorKey($data->major)] = $this->getPlan($data);
            $a++;
        }

        return $result;
    }

    private function getMajorKey($major)
    {
        if ($major == 'riazi' || $major == 'ریاضی') {
            return 'riazi';
        }

        return 'tajrobi';
    }

    private function getPlan($data)
    {
        return [
            'major' => $data->major,
            'lessonName' => $data->lesson_name,
            'sectionName' => $data->section_name,
            'offer' => $data->offer,
            'link' => $data->link,
            'time' => $data->time,
            'voice' => $data->voice,
        ];
    }
}
