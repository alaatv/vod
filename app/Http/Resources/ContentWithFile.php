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
class ContentWithFile extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Models\Content)) {
            return [];
        }

        $this->loadMissing('contenttype', 'section', 'user', 'set');
        $redirectUrl = $this->redirectUrl;
        $authUser = $request->user();
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
            'file' => $this->when($this->hasFile(), function () use ($authUser) {
                if ($this->contenttype_id == config('constants.PRODUCT_FILE_TYPE_PAMPHLET')) {
                    if ($authUser?->roles()->get()->isNotEmpty()) {
                        return $this->getContentExplicitFile();
                    }
                    $canSee = $this->getCanSeeContent($authUser);
                    return ($canSee == 0 || $canSee == 2) ? null : $this->getContentExplicitFile();
                }
                return null;
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
            'short_description' => $this->short_description,
            'published' => !($this->enable == 0 || $this->validSince > now()),
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
