<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\Plan
 *
 * @mixin \App\Plan
 * */
class Plan extends AlaaJsonResource
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
        $contents = $this->contents;
        $teachingContent = $contents->first(function ($content) {
            return $content->pivot->type_id = 4;
        });
        $product = $teachingContent?->set->products->first();
        return [
            'id' => $data->id,
            'title' => $data->title,
            'description' => $data->description,
            'long_description' => $data->long_description,
            'start' => $data->start,
            'end' => $data->end,
            'major' => $this->when(isset($this->major_id), function () {
                return isset($this->major_id) ? new Major($this->major) : null;
            }),
            'backgroundColor' => array_key_exists($product?->id,
                \App\Models\Product::ABRISHAM_2_DATA) ? \App\Models\Product::ABRISHAM_2_DATA[$product?->id]['color'] : null,
            'borderColor' => array_key_exists($product?->id,
                \App\Models\Product::ABRISHAM_2_DATA) ? \App\Models\Product::ABRISHAM_2_DATA[$product?->id]['color'] : null,
            'textColor' => array_key_exists($product?->id,
                \App\Models\Product::ABRISHAM_2_DATA) ? \App\Models\Product::ABRISHAM_2_DATA[$product?->id]['color'] : null,
            'url' => $data->link,
            'voice' => $data->voice,
            'video' => $data->video,
            'contents' => ContentInPlan::collection($contents),
            'grade' => [
                'id' => 8,
                'name' => 'davazdahom',
                'title' => 'دوازدهم',
            ],
            'exam' => [
                'id' => 1,
                'title' => 'قلمچی',
            ],
            'product' => [
                'id' => $product?->id,
                'title' => $product?->shortName,
                'lesson_name' => array_key_exists($product?->id,
                    \App\Models\Product::ABRISHAM_2_DATA) ? \App\Models\Product::ABRISHAM_2_DATA[$product?->id]['lesson_name'] : null,
            ],
            'date' => $this->studyplan?->plan_date,
            'study_method' => new StudyEventMethodResource($this->studyplan->event->studyEventMethod),
        ];
    }

    /**
     * @param
     *
     * @return string|null
     */
    private function getMajorName($majorId): ?string
    {
        if ($majorId == 1) {
            $majorName = 'riazi';
        } else {
            if ($majorId == 2) {
                $majorName = 'tajrobi';
            } else {
                if ($majorId == 3) {
                    $majorName = 'ensani';
                }
            }
        }

        return $majorName ?? null;
    }
}
