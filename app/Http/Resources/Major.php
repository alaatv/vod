<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class Major
 *
 * @mixin \App\Major
 * */
class Major extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $request->user();
        $majorId = optional($user)->major_id;

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name), //We should keep it for Andoid app
            'title' => $this->when(isset($this->name), $this->name),
            'selected' => !is_null($majorId) && $majorId == $this->id,
        ];
    }
}
