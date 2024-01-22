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
class Content extends AlaaJsonResource
{
    use Resource;

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
        $this->loadMissing('contenttype', 'section', 'user', 'set');
        $redirectUrl = $this->redirectUrl;
        $authUser = $request->user();
        if (is_null($authUser)) {
            $authUser = $request->user('api');
        }
        if (is_null($authUser)) {
            $authUser = $request->user();
        }
        $canSeeCode = $this->getCanSeeContent($authUser);
        $canSee = !($canSeeCode == 0 || $canSeeCode == 2);
        $userHasAnyRole = $authUser?->roles()->get()->isNotEmpty();

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'type' => $this->when(isset($this->contenttype_id), function () {
                return $this->getType();
            }),
            'title' => $this->when(isset($this->name), $this->name),
            'body' => $this->getContentBody(),
            'tags' => $this->when(isset($this->tags), function () {
                return $this->getTag();
            }),
            'file' => $this->when($this->hasFile(), function () use ($canSee, $userHasAnyRole) {
                if ($userHasAnyRole) {
                    return $this->getContentExplicitFile();
                }

                return $canSee ? $this->getContentExplicitFile() : null;
            }),
            'duration' => $this->when(isset($this->duration), $this->getDuration()),
            'photo' => $this->when(isset($this->thumbnail), $this->thumbnail),
            'is_free' => $this->isFree,
            'order' => $this->order,
            'page_view' => $this->when(isset($this->page_view), $this->page_view),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'url' => $this->getUrl($this),
            'previous_url' => $this->when(!is_null($this->getPreviousContentForAPIV2()), function () {
                return $this->getUrl($this->getPreviousContentForAPIV2());
            }),
            'next_url' => $this->when(!is_null($this->getNextContentForAPIV2()), function () {
                return $this->getUrl($this->getNextContentForAPIV2());
            }),
            'author' => $this->when(isset($this->author_id), function () {
                return $this->getAuthor();
            }),
            'set' => $this->when(isset($this->contentset_id), function () {
                return $this->getSetInContent();
            }),
            'related_product' => $this->getRelatedProducts(),
            'can_see' => $canSeeCode,
            'recommended_products' => null,
            'timepoints' => $this->getTimePoints(),
            //            'recommended_products' => $this->when($this->recommended_products->isNotEmpty(), $this->getRecommendedProducts()),
            'source' => $this->when($this->sources->isNotEmpty(), function () {
                return $this->sources->isNotEmpty() ? Source::collection($this->sources) : null;
            }),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
            'forrest_trees' => $this->forrest_tree,
            'forrest_tags' => $this->forrest_tags,
            'status' => $this->when(isset($this->content_status_id), function () {
                return new ContentStatusResource($this->status);
            }),
            'vast' => new VastResource($this->vast),
            'is_favored' => $this->is_favored,
            'hls' => $this->when($this->hls, function () use ($canSee, $userHasAnyRole) {
                if ($userHasAnyRole) {
                    return $this->hls;
                }

                return $canSee ? $this->hls : null;
            }),
            'stream' => $this->when($this->stream, function () use ($canSee, $userHasAnyRole) {
                if ($userHasAnyRole) {
                    return $this->streamForApp;
                }

                return $canSee ? $this->streamForApp : null;
            }),
            'can_user_use_timepoint' => $this->can_user_use_timepoint,
            'has_vast' => isset($this->vast_url),
        ];
    }

    private function getType()
    {
        //        return New Contenttype($this->contenttype);
        return $this->contenttype_id;
    }

    /**
     * @return string|null
     */
    private function getContentBody()
    {
        if ($this->isArticle()) {
            $body = $this->context;
        } else {
            $body = $this->description;
        }

        return $body;
    }

    private function getTag()
    {
        return new Tag($this->tags);
    }

    private function getDuration()
    {
        if ($this->isVideo()) {
            return isset($this->duration) ? gmdate('H:i', $this->duration) : $this->duration;
        }

        return $this->duration;
    }

    private function getUrl($content)
    {
        return new Url($content);
    }

    private function getRelatedProducts()
    {
        if (!$this->isFree) {
            $relatedProduct = optional($this->activeProducts())->first();
        } else {
            $relatedProduct = optional($this->related_products)->first();
        }
        if (!isset($relatedProduct)) {
            return null;
        }

        return new ProductInBlockWithoutPagination($relatedProduct);
    }

    /**
     * @return mixed
     */
    private function getRecommendedProducts()
    {
        return $this->recommended_products->isNotEmpty() ? $this->recommended_products : null;
    }
}
