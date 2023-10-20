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
class SetInSearch extends AlaaJsonResource
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

        $redirectUrl = $this->redirect_url;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'title' => $this->when(isset($this->name), $this->name),
            'short_title' => $this->when(isset($this->shortName), $this->shortName),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'url' => $this->when($this->hasUrl(), $this->hasUrl() ? new Url($this) : null),
            'contents_count' => $this->activeContentsForApiV2->count(),
            'author' => $this->when(isset($this->author), $this->getAuthor()),
            'contents' => null,
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
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
            return null;
        }

        return new Author($this->author);
    }

    private function getTags()
    {
        return new Tag($this->tags);
    }
}
