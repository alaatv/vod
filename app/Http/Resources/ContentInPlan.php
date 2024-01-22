<?php

namespace App\Http\Resources;

use App\Models\ContentOfPlanType;
use App\Traits\Content\Resource;
use Illuminate\Http\Request;

/**
 * Class Content
 *
 * @mixin \App\Models\Content
 * */
class ContentInPlan extends AlaaJsonResource
{
    use Resource;

    public function __construct(\App\Models\Content $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Models\Content)) {
            return [];
        }

        $authUser = $request->user();

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($this->redirectUrl), $this->redirectUrl),
            'type' => $this->when(isset($this->pivot), function () {
                $type = optional($this->pivot)->type_id;
                if ($type) {
                    return new ContentofplanTypeResource(optional(ContentOfPlanType::find($type)));
                }

                return null;
            }),
            'title' => $this->when(isset($this->name), $this->name),
            'url' => new Url($this),
            'file' => $this->when($this->hasFile(), function () use ($authUser) {
                $canSee = $this->getCanSeeContent($authUser);

                return ($canSee == 0 || $canSee == 2) ? null : $this->getContentFile();
            }),
            'photo' => $this->when(isset($this->thumbnail), $this->thumbnail),
            'can_see' => $this->getCanSeeContent($authUser),
            'has_watched' => !is_null($authUser) && $authUser->hasWatched($this->id),
            'set' => $this->when(isset($this->contentset_id), function () {
                return $this->getSetInContent();
            }),
        ];
    }
}
