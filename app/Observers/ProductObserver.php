<?php

namespace App\Observers;

use App\Classes\Search\Tag\TaggingInterface;
use App\Jobs\DanaEditCourseJob;
use App\Models\Contentset;

use App\Models\Product;
use App\Traits\APIRequestCommon;
use App\Traits\TaggableTrait;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    private $tagging;

    use TaggableTrait;
    use APIRequestCommon;

    public function __construct(TaggingInterface $tagging)
    {
        $this->tagging = $tagging;
    }

    /**
     *
     *
     * @param  int  $order
     *
     * @return void
     */
    public static function shiftProductOrders($order): void
    {
        $productsWithSameOrder = Product::getProducts(0, 0)
            ->where('order', $order)
            ->get();
        foreach ($productsWithSameOrder as $productWithSameOrder) {
            $productWithSameOrder->order = $productWithSameOrder->order + 1;
            $productWithSameOrder->update();
        }
    }

    /**
     * Handle the product "created" event.
     *
     * @param  Product  $product
     *
     * @return void
     */
    public function created(Product $product)
    {
        Cache::tags([
            'landing',
            'product_valid_file',
        ])->flush();

    }

    /**
     * Handle the product "updated" event.
     *
     * @param  Product  $product
     *
     * @return void
     */
    public function updated(Product $product)
    {
        Cache::tags([
            'landing',
            'product_valid_file',
        ])->flush();
//        if($product->isActive())
//        {
//            dispatch(new UpdateInSearchia($product));
//        }
//        else{
//            dispatch(new DeleteFromSearchia($product));
//        }
    }

    /**
     * Handle the product "deleted" event.
     *
     * @param  Product  $product
     *
     * @return void
     */
    public function deleted(Product $product)
    {
        Cache::tags([
            'landing',
        ])->flush();
    }

    /**
     * Handle the product "restored" event.
     *
     * @param  Product  $product
     *
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the product "force deleted" event.
     *
     * @param  Product  $product
     *
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }

    /**
     * When issuing a mass update via Eloquent,
     * the saved and updated model events will not be fired for the updated models.
     * This is because the models are never actually retrieved when issuing a mass update.
     *
     * @param  Product  $product
     */
    public function saving(Product $product)
    {


    }

    public function saved(Product $product)
    {
        //todo
//        self::shiftProductOrders($product->order);

        $this->sendTagsOfTaggableToApi($product, $this->tagging);

        $introducerContents = optional($product->sample_contents)->tags;
        $this->setRelatedContentsTags($product, isset($introducerContents) ? $introducerContents : [],
            Product::SAMPLE_CONTENTS_BUCKET);

        $recommenderItems = optional($product->recommender_contents)->recommenders;
        $recommenderContents = optional($recommenderItems)->contents;
        $recommenderSets = optional($recommenderItems)->sets;
        $this->setRecommenderContentsTags($product, !is_null($recommenderContents) ? $recommenderContents : [],
            !is_null($recommenderSets) ? $recommenderSets : [], Product::RECOMMENDER_CONTENTS_BUCKET);

//        dispatch(new SendProductForAds($product));

        Cache::tags([
            'product_'.$product->id,
            'product_search',
            'relatedProduct',
            'productCollection',
            'shop',
            'home',
            'recommendedProduct',
            'landing',
            'userAsset',
        ])->flush();
        DanaEditCourseJob::dispatch($product, ['hardship' => $product->hardship?->specifier_value]);
    }

    private function setRelatedContentsTags(Product $product, array $contentIds, string $bucket): bool
    {
        $itemTagsArray = [];
        foreach ($contentIds as $id) {
            $itemTagsArray[] = 'Content-'.$id;
        }

        $params = [
            'tags' => json_encode($itemTagsArray, JSON_UNESCAPED_UNICODE),
        ];

        $response = $this->sendRequest(config('constants.TAG_API_URL')."id/$bucket/".$product->id, 'PUT', $params);
        return true;
    }

    private function setRecommenderContentsTags(
        Product $product,
        array $contentIds,
        array $setIds,
        string $bucket
    ): bool {
        $itemTagsArray = [];
        $itemTagsArray = array_merge($itemTagsArray, $contentIds);

        foreach ($setIds as $id) {
            $set = Contentset::Find($id);
            if (!isset($set)) {
                continue;
            }

            $itemTagsArray = array_merge($itemTagsArray, $set->contents->pluck('id')->toArray());
        }

        $params = [
            'tags' => json_encode($itemTagsArray, JSON_UNESCAPED_UNICODE),
        ];

        $response = $this->sendRequest(config('constants.TAG_API_URL')."id/$bucket/".$product->id, 'PUT', $params);
        return true;
    }
}
