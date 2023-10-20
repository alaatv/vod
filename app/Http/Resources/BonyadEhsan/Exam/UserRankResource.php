<?php

namespace App\Http\Resources\BonyadEhsan\Exam;

use App\Http\Resources\AlaaJsonResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class UserRankResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'title' => $this['title'],
            'lessons' => $this['lessons'],
        ];
    }
}
