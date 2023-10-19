<?php

namespace App\Http\Resources;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Product;
use App\Traits\Content\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class Content
 *
 * @var Contentset $contentSet
 * @var Product $product
 * @mixin Content
 * */
class ContentInSetWithoutPagination extends AlaaJsonResource
{
    use Resource;

    public function __construct(Content $model)
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
        if (!($this->resource instanceof Content)) {
            return [];
        }

        $this->loadMissing('contenttype', 'section', 'user', 'set');
        $redirectUrl = $this->redirect_url;

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
            'order' => $this->order,
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'url' => new Url($this),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
            'is_favored' => $this->is_favored,
            'set' => (object) ['id' => $this->contentset_id]
        ];
    }

    private function getType()
    {
        return $this->contenttype_id;
    }
}
