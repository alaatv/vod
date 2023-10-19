<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\ContentTypeResource;
use App\Traits\Content\Resource;
use Illuminate\Http\Request;


/**
 * Class Content
 *
 * @mixin \App\Content
 */
class ContentForStudyPlanResource extends AlaaJsonResource
{
    use Resource;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Content)) {
            return [];
        }

        $this->loadMissing('set');
        $authUser = $request->user('api');

        return [
            'id' => $this->id,
            'title' => $this->when(isset($this->name), $this->name),
            'photo' => $this->when(isset($this->thumbnail), $this->thumbnail),
            'file' => $this->when($this->hasFile(), function () use ($authUser) {
                $canSee = $this->getCanSeeContent($authUser);
                return ($canSee == 0 || $canSee == 2) ? null : $this->getContentExplicitFile();
            }),
            'content_type' => $this->when(isset($this->contenttype_id), function () {
                return new ContentTypeResource($this->contenttype);
            }),
            'has_watched' => !is_null($request->user()) && $request->user()->hasWatched($this->id),
            'url' => new Url($this),
            'short_title' => $this->abrishamProductShortTitle($this->plan_major?->id, true),
            'start' => $this->when(isset($this->plan_start), $this->plan_start),
            'end' => $this->when(isset($this->plan_end), $this->plan_end),
            'is_current' => $this->when(isset($this->plan_is_current), $this->plan_is_current),
            'lesson_name' => optional($this->set)->abrishamProductLessonName($this->plan_major?->id),
            'major' => $this->when(isset($this->plan_major), $this->plan_major),
            'can_see' => $this->getCanSeeContent($authUser),
            'is_favored' => $this->getIsFavored(),
            'order' => $this->order,
            'set' => $this->when(isset($this->contentset_id), function () {
                return $this->getSetInContent();
            }),
            'author' => $this->when(isset($this->author_id), function () {
                return $this->getAuthor();
            }),
            'timepoints' => $this->getTimePoints(),
        ];
    }
}
