<?php

namespace App\Http\Resources;

use App\Traits\User\AssetTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Bookmark extends AlaaJsonResource
{
    use AssetTrait;

    public const TYPE_CONTENT = 'content';
    public const TYPE_PRODUCT = 'product';
    public const TYPE_SET = 'set';

    private $item;
    private $type;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->item = Arr::get($this->resource, 'items');
        $this->type = Arr::get($this->resource, 'type');

        return [
            'id' => $this->item->id,
            'title' => optional($this->item)->name,
            'photo' => $this->getPhoto(),
            'type' => $this->type,
            'url' => $this->getUrl(),
            'timepoint' => $this->getTimePoints(),
            'is_purchased' => ($this->type == 'product') ?
                $this->searchProductInUserAssetsCollection($this->item, auth()->user()) : null
        ];
    }

    private function getPhoto(): ?string
    {
        if ($this->type == self::TYPE_PRODUCT || $this->type == self::TYPE_SET) {
            return $this->item->photo;
        } elseif ($this->type == self::TYPE_CONTENT) {
            return $this->item->thumbnail;
        }

        return null;
    }

    private function getUrl()
    {
        if ($this->type == self::TYPE_PRODUCT || $this->type == self::TYPE_CONTENT) {
            return new Url($this->item);
        } elseif ($this->type == self::TYPE_SET) {
            return new Url($this->item);
        }

        return null;
    }

    private function getTimePoints()
    {
        if ($this->type != self::TYPE_CONTENT) {

            return null;
        }
        $timepoints = optional($this->item)->favored_times;
        if ($timepoints->isEmpty()) {
            return null;
        }
        return ContentTimePointAPI::collection($timepoints);
    }
}
