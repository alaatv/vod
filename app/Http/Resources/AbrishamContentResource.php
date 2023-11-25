<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\ContentTypeResource;
use App\Traits\Content\Resource;
use Illuminate\Support\Arr;

/**
 * Class Content
 *
 * @mixin \App\Models\Content
 * */
class AbrishamContentResource extends AlaaJsonResource
{
    use Resource;

    public function __construct(\App\Models\Content $model)
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
        if (!($this->resource instanceof \App\Models\Content)) {
            return [];
        }

        $this->loadMissing('contenttype', 'section', 'user', 'set');
        $redirectUrl = $this->redirect_url;
        $authUser = $request->user() ?? null;
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
                return ($canSee == 0 || $canSee == 2) ? null : $this->getContentExplicitFile();
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
            'comments' => $this->when(!is_null($authUser), function () use ($authUser) {
                return !is_null($authUser) ? CommentLightResource::collection($this->comments()->where('author_id',
                    $authUser->id)->get()) : [];
            }),
            'short_title' => $this->abrishamProductShortTitle(),
            'has_watched' => !is_null($authUser) && $authUser->hasWatched($this->id),
            'content_type' => $this->when(isset($this->contenttype_id), function () {
                return new ContentTypeResource($this->contenttype);
            }),
            'is_favored' => $this->getIsFavored(),
            'author' => $this->when(isset($this->author_id), function () {
                return $this->getAuthor();
            }),
            'set' => $this->when(isset($this->contentset_id), function () {
                return $this->getSetInContent();
            }),
            'timepoints' => $this->getTimePoints(),
        ];
    }

    private function getType()
    {
//        return New Contenttype($this->contenttype);
        return $this->contenttype_id;
    }
}
