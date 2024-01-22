<?php

namespace App\Http\Resources;

use App\Models\Content;
use App\Models\Contentset;
use App\Traits\Set\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class Set
 *
 * @mixin Contentset
 * */
class ProductSet extends AlaaJsonResource
{
    use Resource;

    public function __construct(Contentset $model)
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
        if (!($this->resource instanceof Contentset)) {
            return [];
        }

        $activeContents = optional($this->activeContents)->where('contenttype_id', Content::CONTENT_TYPE_VIDEO);
        /** @var Content $lastContent */
        $lastContent = optional($activeContents)->last();
        $redirectUrl = $this->redirect_url;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'title' => $this->when(isset($this->name), $this->name),
            'short_title' => $this->when(isset($this->shortName), $this->shortName),
            //            'photo'          => $this->when(isset($this->photo), $this->photo),
            'photo' => $this->when(isset($this->photo), $this->getPhoto($lastContent)),
            'url' => $this->when($this->hasUrl(), $this->hasUrl() ? new Url($this) : null),
            'contents_count' => $activeContents->count(),
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

    private function getPhoto(?Content $lastContent): ?string
    {
        return (isset($this->photo)) ? $this->photo : optional($lastContent)->thumbnail;
    }
}
