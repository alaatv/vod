<?php

namespace App\Http\Resources\Abrisham;

use App\Http\Resources\AlaaJsonResource;
use Illuminate\Http\Request;

/**
 * Class AbrishamLessonResource
 */
class AbrishamLessonResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        return [
            'title' => $this['title'],
            'lessons' => $this['lessons'],
        ];
    }
}
