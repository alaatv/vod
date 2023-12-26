<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class \App\Models\MapDetail
 *
 * @mixin \App\Models\MapDetail
 * */
class MapDetail extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                'id' => $this->id,
                'map_id' => $this->map_id,
                'type_id' => $this->type_id,
                'min_zoom' => $this->min_zoom,
                'max_zoom' => $this->max_zoom,
                'action' => $this->action,
                'data' => array_merge([
                    'lat_lngs' => LatLngResource::collection($this->latlngs),
                ], json_decode(json_encode($this->data), true)),
                'tags' => $this->tags,
                'enable' => $this->enable,
                'entity' => (isset($this->entity_id) || isset($this->entity_type)) ? new Entity($this) : null,
            ];
    }
}
