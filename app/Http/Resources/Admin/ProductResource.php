<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\Attribute as AttributeNonAdminResource;
use App\Http\Resources\IntroVideoOfProduct;
use App\Http\Resources\SampleVideoBlock;
use App\Models\Product;
use App\Traits\Product\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class ProductResource
 *
 * @mixin Product
 */
class ProductResource extends AlaaJsonResource
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
        if (!($this->resource instanceof Product)) {
            return [];
        }

        $this->loadMissing('sets', 'children', 'producttype', 'attributeset', 'grand');
        $redirectUrl = $this->redirect_url;

        return [

            // TODO: Merge The two blocks of code

            'type' => $this->when(isset($this->producttype_id), $this->getType()),
            'title' => $this->when(isset($this->name), $this->name),
            'price' => $this->getPrice(),
            'intro' => $this->when(isset($resource->intro_video),
                isset($this->intro_video) ? new IntroVideoOfProduct($this) : null),
            'url' => $this->getUrl(),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'sample_photos' => $this->when($this->hasSamplePhoto(), $this->getSamplePhoto()), //It is not a relationship
            'sets' => $this->when($this->sets->isNotEmpty(), $this->getSet()),
            'blocks' => $this->when(optional($this)->blocks->isNotEmpty(), function () {
                return optional($this)->blocks->isNotEmpty() ? SampleVideoBlock::collection(optional($this)->blocks) : null;
            }),
            'attributes' => new AttributeNonAdminResource($this),
            'children' => $this->when($this->children->isNotEmpty(), $this->getChildren()),
            'checked' => true,
//            'is_favored'     => $this->is_favored ,


            'id' => $this->id,
            'category' => $this->when(isset($this->category), $this->category),
            'grand' => $this->when(optional($this)->blocks->isNotEmpty(), function () {
                return new ProductResource($this->grand);
            }),
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'name' => $this->when(isset($this->name), $this->name),
            'base_price' => $this->basePrice,
            'discount' => $this->discount,
            'is_free' => $this->isFree,
            'amount' => $this->when(isset($this->amount), $this->amount),
            'description' => $this->getDescription(),
            'tags' => $this->when(isset($this->tags), $this->getTags()),
            'recommender_contents' => $this->when(isset($this->recommender_contents), $this->recommender_contents),
            'sample_contents' => $this->when(isset($this->sample_contents), $this->sample_contents),
            'slogan' => $this->when(isset($this->slogan), $this->slogan),
            'image' => $this->when(isset($this->image), $this->image),
            'file' => $this->when(isset($this->file), $this->file),
            'intro_videos' => $this->when(isset($this->intro_videos), $this->intro_videos),
            'valid_since' => $this->when(isset($this->validSince), $this->validSince),
            'valid_until' => $this->when(isset($this->validUntil), $this->validUntil),
            'enable' => $this->enable,
            'display' => $this->display,
            'order' => $this->order,
            'bonPlus' => $this->bon_plus,
            'bonDiscount' => $this->bon_discount,
            'jalaliValidSince' => $this->when(isset($this->validSince), $this->jalaliValidSince),
            'jalaliValidUntil' => $this->when(isset($this->validUntil), $this->jalaliValidUntil),
            'jalaliValidCreatedAt' => $this->when(isset($this->created_at), $this->jalaliCreatedAt),
            'jalaliValidUpdatesAt' => $this->when(isset($this->updated_at), $this->jalaliUpdatedAt),
            'editLink' => $this->when(hasAuthenticatedUserPermission(config('constants.EDIT_PRODUCT_ACCESS')),
                function () {
                    return action('Web\ProductController@edit', $this->id);
                }),
            'removeLink' => $this->when(hasAuthenticatedUserPermission(config('constants.REMOVE_PRODUCT_ACCESS')),
                function () {
                    return action('Web\ProductController@destroy', $this->id);
                }),
            'page_view' => $this->when(isset($this->page_view), $this->page_view),
            'product_type' => $this->when(isset($this->producttype_id), function () {
                return new ProductTypeResource($this->producttype);
            }),
            'attribute_set' => $this->when(isset($this->attributeset_id), function () {
                return new AttributeSetResource($this->attributeset);
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
        ];
    }
}
