<?php

namespace App\Http\Resources;

use App\Traits\Content\Resource;
use Illuminate\Support\Arr;

/**
 * Class Content
 *
 * @mixin \App\Content
 * */
class ContentInSetWithFileWithoutPagination extends AlaaJsonResource
{
    use Resource;

    public function __construct(\App\Content $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     *
     * @param $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Content)) {
            return [];
        }

        $this->loadMissing('contenttype', 'section', 'user', 'set');
        $redirectUrl = $this->redirect_url;
        $authUser = $request->user();


        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'type' => $this->when(isset($this->contenttype_id), $this->getType()), // ToDo : Should be removed
            'section' => $this->when(isset($this->section_id), function () {
                return new Section($this->section);
            }),
            'title' => $this->when(isset($this->name), $this->name),
            'file' => $this->when($this->hasFile(), function () use ($authUser) {
                $canSee = $this->getCanSeeContent($authUser);
                return ($canSee == 0 || $canSee == 2) ? null : $this->getContentFile();
            }),
            'duration' => $this->when(isset($this->duration), $this->duration),
            'photo' => $this->when(isset($this->thumbnail), $this->thumbnail),
            'is_free' => $this->isFree,
            'order' => $this->order,
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'url' => new Url($this),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
        ];
    }


}
