<?php

namespace App\Http\Resources;

use App\Traits\Content\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class Content
 *
 * @mixin \App\Models\Content
 * */
class ContentInSetForWebWithoutPagination extends AlaaJsonResource
{
    use Resource;

    public function __construct(\App\Models\Content $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Models\Content)) {
            return [];
        }

        $this->loadMissing('contenttype', 'section', 'user', 'set');

        $favoredTimes = ContentTimePointWeb::collection($this->favored_times);
        $redirectUrl = $this->redirectUrl;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'type' => $this->when(isset($this->contenttype_id), $this->getType()),
            'section' => $this->when(isset($this->section_id), function () {
                return new Section($this->section);
            }),
            'title' => $this->when(isset($this->name), $this->name),
            'duration' => $this->when(isset($this->duration), $this->duration),
            'photo' => $this->when(isset($this->thumbnail), $this->thumbnail),
            'is_free' => $this->isFree,
            'is_favored' => $this->getIsFavored(),
            'order' => $this->order,
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'url' => new Url($this),
            'timepoints' => $this->when($favoredTimes->isNotEmpty(), function () use ($favoredTimes) {
                return $favoredTimes->isNotEmpty() ? $favoredTimes : null;
            }),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
        ];
    }

    private function getType()
    {
//        return New Contenttype($this->contenttype);
        return $this->contenttype_id;
    }
}
