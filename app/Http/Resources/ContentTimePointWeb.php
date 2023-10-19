<?php

namespace App\Http\Resources;

use App\Models\Timepoint;
use Illuminate\Http\Request;


/**
 * Class Timepoint
 *
 * @mixin Timepoint
 * */
class ContentTimePointWeb extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isFavored = $this->isFavored;
        if (!isset($isFavored)) {
            $isFavored = (auth()->check()) ? auth()->user()->hasFavoredTimepoint($this->resource) : false;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'time' => $this->time,
            'isFavored' => $isFavored,
            'favorUrl' => route('web.mark.favorite.content.timepoint', ['timepoint' => $this->resource->id]),
            'unfavorUrl' => route('web.mark.unfavorite.content.timepoint', ['timepoint' => $this->resource->id]),
        ];
    }
}
