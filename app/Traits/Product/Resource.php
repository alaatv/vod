<?php


namespace App\Traits\Product;


use App\Http\Resources\Child;
use App\Http\Resources\Gift;
use App\Http\Resources\Price;
use App\Http\Resources\ProductSamplePhoto;
use App\Http\Resources\ProductSet;
use App\Http\Resources\Tag;
use App\Http\Resources\Url;

trait Resource
{

    /**
     * @return Url
     */
    private function getUrl(): Url
    {
        return new Url($this);
    }

    /**
     * @return Price
     */
    private function getPrice(): Price
    {
        return new Price($this->price);
    }

    private function getChildren()
    {
        $children = $this->active_children;
        return $children->isNotEmpty() ? Child::collection($children) : null;
    }

    private function getTags()
    {
        return isset($this->tags) ? new Tag($this->tags) : null;
    }

    private function getGift()
    {
        return $this->gift->isNotEmpty() ? Gift::collection($this->gift) : null;
    }

    private function getSet()
    {
        return $this->sets->isNotEmpty() ? ProductSet::collection($this->whenLoaded('sets')) : null;
    }

    private function getSamplePhoto()
    {
        return $this->hasSamplePhoto() ? ProductSamplePhoto::collection($this->sample_photos) : null;
    }

    /**
     * @return bool
     */
    private function hasSamplePhoto(): bool
    {
        return isset($this->sample_photos) && $this->sample_photos->isNotEmpty();
    }

    private function getType()
    {
//        return new Producttype($this->producttype);
        return $this->producttype_id;
    }

    /**
     * @return array
     */
    private function getDescription(): ?array
    {
        if (!isset($this->shortDescription) && !isset($this->longDescription)) {
            return null;
        }
        return [
            'slogan' => $this->when(isset($this->slogan), $this->slogan),
            'short' => $this->when(isset($this->shortDescription), $this->shortDescription),
            'long' => $this->when(isset($this->longDescription), $this->longDescription),
        ];
    }
}
