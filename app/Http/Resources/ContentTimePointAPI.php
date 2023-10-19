<?php

namespace App\Http\Resources;

use App\Models\Timepoint;
use App\Models\Timepoint;
use Illuminate\Http\Request;


/**
 * Class Timepoint
 *
 * @mixin Timepoint
 * */
class ContentTimePointAPI extends AlaaJsonResource
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
        if (is_null($isFavored)) {
            $isFavored = auth('alaatv')->check() ? auth('alaatv')->user()->hasFavoredTimepoint($this->resource) : false;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'time' => $this->time,
            'isFavored' => $isFavored,
            'favorUrl' => route('api.v2.mark.favorite.content.timepoint', ['timepoint' => $this->resource->id]),
            'unfavorUrl' => route('api.v2.mark.unfavorite.content.timepoint', ['timepoint' => $this->resource->id]),
        ];
    }
}
