<?php

namespace App\Http\Resources;


class FlatAbrishamLessonResource extends AlaaJsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'title' => $this['title'],
        ];
    }
}
