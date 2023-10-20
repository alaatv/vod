<?php

namespace App\Http\Resources;

use App\Models\Contentset;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class Set
 *
 * @mixin Contentset
 * */
class SetWithoutPagination extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Contentset)) {
            return [];
        }

        $activeContents = $this->getActiveContents2();
        $redirectUrl = $this->redirect_url;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'title' => $this->when(isset($this->name), $this->name),
            'short_title' => $this->when(isset($this->shortName), $this->shortName),
            'photo' => $this->when(isset($this->photo), $this->photo),
//            'tags'                  => $this->when(isset($this->tags), $this->getTags()),
            'contents_count' => $this->active_contents_count,
            'url' => $this->when($this->hasUrl(), function () {
                return $this->hasUrl() ? new Url($this) : null;
            }),
            'author' => $this->when(isset($this->author), function () {
                return isset($this->author) ? $this->getAuthor() : null;
            }),
            'contents' => $this->when($activeContents->isNotEmpty(), function () use ($activeContents) {
                return $activeContents->isNotEmpty() ? ContentInSetWithoutPagination::collection($activeContents) : null;
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'source' => $this->when($this->sources->isNotEmpty(), function () {
                return $this->sources->isNotEmpty() ? Source::collection($this->sources) : null;
            }),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
        ];
    }

    private function hasUrl()
    {
        return isset($this->url) || isset($this->api_url_v2);
    }

    private function getAuthor()
    {
        if (!isset($this->author)) {
            //Note:It is like this because of android ! please don't change it
            return [
                'id' => null,
                'first_name' => null,
                'last_name' => null,
                'photo' => null,
            ];
        }
        return new Author($this->author);
    }

    private function getTags()
    {
        return new Tag($this->tags);
    }
}
