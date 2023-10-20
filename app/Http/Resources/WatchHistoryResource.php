<?php

namespace App\Http\Resources;

use App\Models\WatchHistory;
use App\Traits\MorphTrait;
use Illuminate\Http\Request;


/**
 * Class WatchHistoryResource
 *
 * @mixin WatchHistory
 */
class WatchHistoryResource extends AlaaJsonResource
{
    use MorphTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  Request|WatchHistory  $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        if (!($this->resource instanceof WatchHistory)) {
            return [];
        }

        return [
            'id' => $this->id,
            'watchable_id' => $this->watchable_id,
            'watchable_type' => $this->watchable_type,
            'watchable' => $this->when($this->relationLoaded('resource'), function () {
                return $this->getResourceByModel('watchable');
            }),
            'seconds_watched' => $this->when(isset($this->seconds_watched), $this->seconds_watched),
            'completely_watched' => $this->completely_watched,
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
        ];
    }
}
