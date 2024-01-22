<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class Block
 *
 * @mixin \App\Models\Block
 * */
class BlockInAsset extends AlaaJsonResource
{
    public function __construct(\App\Models\Block $model)
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
        if (!($this->resource instanceof \App\Models\Block)) {
            return [

            ];
        }

        $contents = $this->contents->where('enable', 1)->where('redirectUrl', null);
        $sets = $this->sets->where('enable', 1)->where('redirectUrl', null);
        $products = $this->products;
        $banners = $this->banners;

        return [
            'id' => $this->id,
            'title' => $this->when(isset($this->title), $this->title),
            'offer' => $this->when(isset($this->offer), $this->offer),
            'url' => $this->when(isset($this->url_v2), isset($this->url_v2) ? new UrlForBlock($this) : null),
            'order' => $this->order,
            'contents' => $this->when($this->collectionIsNotEmpty($contents), $this->getContents($contents)),
            'sets' => $this->when($this->collectionIsNotEmpty($sets), $this->getSets($sets)),
            'products' => $this->when($this->collectionIsNotEmpty($products), $this->getProducts($products)),
            'banners' => $this->when($this->collectionIsNotEmpty($banners), $this->getBanners($banners)),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'subscriptions' => $this->subscriptions,
        ];
    }

    private function collectionIsNotEmpty($collection)
    {
        return isset($collection) && $collection->isNotEmpty();
    }

    private function getContents($contents)
    {
        return $this->collectionIsNotEmpty($contents) ? ContentInSetWithoutPagination::collection($contents) : null;
    }

    private function getSets($sets)
    {
        return $this->collectionIsNotEmpty($sets) ? SetInIndexWithoutPagination::collection($sets) : null;
    }

    private function getProducts($products)
    {
        $products->load('sets');

        return $this->collectionIsNotEmpty($products) ? ProductInAssetWithoutPagination::collection($products) : null;
    }

    private function getBanners($banners)
    {
        return $this->collectionIsNotEmpty($banners) ? SlideshowWithoutPagination::collection($banners) : null;
    }
}
