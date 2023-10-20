<?php

namespace App\Http\Resources;

use App\Traits\Product\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class Product
 *
 * @mixin \App\Product
 */
class ProductForWeb extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Product)) {
            return [];
        }

        $this->loadMissing('sets', 'children', 'producttype');
        $redirectUrl = $this->redirect_url;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'type' => $this->when(isset($this->producttype_id), $this->getType()),
            'category' => $this->when(isset($this->category), $this->category),
            'title' => $this->when(isset($this->name), $this->name),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'tags' => $this->when(isset($this->tags), $this->getTags()),
            'intro' => $this->when(isset($resource->intro_video),
                isset($this->intro_video) ? new IntroVideoOfProduct($this) : null),
            'url' => $this->getUrl(),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'sample_photos' => $this->when($this->hasSamplePhoto(), $this->getSamplePhoto()), //It is not a relationship
            'sets' => $this->when($this->sets->isNotEmpty(), $this->getSet()),
            'blocks' => $this->when(optional($this)->blocks->isNotEmpty(), function () {
                return optional($this)->blocks->isNotEmpty() ? SampleVideoBlock::collection(optional($this)->blocks()->paginate(10)) : null;
            }),
            'attributes' => new Attribute($this),
            'children' => $this->when($this->children->isNotEmpty(), $this->getChildren()),
            'page_view' => $this->when(isset($this->page_view), $this->page_view),
            'checked' => true,
//            'is_favored'     => $this->is_favored ,
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
            'catalog' => $this->catalog,
        ];
    }
}
