<?php namespace App\Traits;

use App\Classes\Uploader\Uploader;
use App\Http\Requests\Request;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\Product;
use App\Models\User;
use App\Models\WatchHistory;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\{Arr};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


trait ProductCommon
{
    /**
     * @param  Product  $product
     * @param           $extraAttributeValues
     *
     * @return int|float
     */
    public function productExtraCostFromAttributes(Product $product, $extraAttributeValues)
    {
        $key =
            'product:productExtraCostFromAttributes:'."\\".$product->cacheKey()."\\extraAttributeValues:".(isset($extraAttributeValues) ? implode('',
                $extraAttributeValues) : '-');

        return (int) Cache::tags(['product', 'product_'.$product->id])
            ->remember($key, config('constants.CACHE_60'), function () use ($product, $extraAttributeValues) {
                $totalExtraCost = 0;
                foreach ($extraAttributeValues as $attributevalueId) {
                    $extraCost = 0;
                    $attributevalue = $product->attributevalues->where('id', $attributevalueId)
                        ->first();

                    if (isset($attributevalue) && isset($attributevalue->pivot->extraCost)) {
                        $extraCost = $attributevalue->pivot->extraCost;
                    }

                    $totalExtraCost += $extraCost;
                }

                return $totalExtraCost;
            });
    }

    /**
     * Finds product intended child based on specified attribute values
     *
     * @param  Product  $product
     * @param  array  $mainAttributeValues
     *
     * @return Product
     */
    public function findProductChildViaAttributes(Product $product, array $mainAttributeValues): ?Product
    {
        foreach ($product->children as $child) {
            $childAttributevalues = $child->attributevalues;
            $flag = true;
            if (isset($mainAttributeValues)) {
                foreach ($mainAttributeValues as $attributevalue) {
                    if (!$childAttributevalues->contains($attributevalue)) {
                        $flag = false;
                        break;
                    }
                }
            }

            if ($flag && $childAttributevalues->count() == count($mainAttributeValues)) {
                $simpleProduct = $child;
                break;
            }
        }
        if (isset($simpleProduct)) {
            return $simpleProduct;
        }
        return null;
    }

    /**
     * Copies a product files to another product
     *
     * @param  Product  $sourceProduct
     * @param  Product  $destinationProduct
     */
    public function copyProductFiles(Product $sourceProduct, Product $destinationProduct): void
    {
        $destinationProductFiles = $sourceProduct->productfiles;
        foreach ($destinationProductFiles as $file) {
            $newFile = $file->replicate();
            $newFile->product_id = $destinationProduct->id;
            $newFile->save();
        }
    }

    /**
     * @param  Product  $sourceProduct
     * @param  Product  $destinationProduct
     * @param  array  $newPhotoInfo
     */
    public function copyProductPhotos(
        Product $sourceProduct,
        Product $destinationProduct,
        array $newPhotoInfo = []
    ): void {
        $destinationProductPhotos = $sourceProduct->photos;
        foreach ($destinationProductPhotos as $photo) {
            $newPhoto = $photo->replicate();
            $newPhoto->product_id = $destinationProduct->id;
            $newPhoto->save();

            if (isset($newPhotoInfo['title'])) {
                $newPhoto->title = $newPhotoInfo['title'];
                $newPhoto->update();
            }
            if (isset($newPhotoInfo['description'])) {
                $newPhoto->description = $newPhotoInfo['description'];
                $newPhoto->update();
            }
        }
    }

    /**
     * @param  User  $user
     * @param  Product  $product
     * @return Content|Content[]|\Illuminate\Database\Eloquent\Collection|Model|JsonResponse|null
     */
    public function cachedNextWatchContent(User $user, Product $product)
    {
        $key = "product:nextWatchContent:product_id_{$product->id}:user_id_{$user->id}";
        return Cache::tags([
            'product',
            'product_content',
            'product_nextWatchContent',
            "product_{$product->id}_nextWatchContent",
            "user_{$user->id}_nextWatchContent",
            "user_{$user->id}_product_{$product->id}_nextWatchContent",
        ])
            ->remember($key, config('constants.CACHE_300'), function () use ($user, $product) {

                $productSets = $product->sets()
                    ->nameDoesNotContain(Contentset::NEXT_WATCH_CONTENT_NOT_CONTAIN_CONTENT_SET_STRINGS)
                    ->smallNameDoesNotContain(Contentset::NEXT_WATCH_CONTENT_NOT_CONTAIN_CONTENT_SET_STRINGS)
                    ->orderBy('pivot_order')
                    ->orderBy('id')
                    ->get();

                // Product isn't valid if it has no ContentSet.
                if (empty($productSets)) {
                    return null;
                }

                $productAllContentIds = [];
                foreach ($productSets as $set) {
                    /** @var Contentset $set */
                    $AllContentIds = $set->activeContents()
                        ->where('contenttype_id', Content::CONTENT_TYPE_VIDEO)
                        ->orderBy('order')
                        ->orderBy('id')
                        ->pluck('id')
                        ->toArray();
                    $productAllContentIds = array_merge($productAllContentIds, $AllContentIds);
                }

                // Product isn't valid if it has no Content.
                if (empty($productAllContentIds)) {
                    return null;
                }

                $watchedContentIds = WatchHistory::watchableType('content')
                    ->whereIn('watchable_id', $productAllContentIds)
                    ->where('user_id', $user->id)
                    ->get()
                    ->pluck('watchable_id')
                    ->toArray();

                // Return first product's content if user hasn't watched any of them.
                if (empty($watchedContentIds)) {
                    return Content::find($productAllContentIds[0]);
                }

                $orderedWatchedContentIds = array_intersect($productAllContentIds, $watchedContentIds);

                $lastWatchedContentId = end($orderedWatchedContentIds);
                $lastOrderedProductAllContentId = end($productAllContentIds);

                // Return last product's content if user watched last of them.
                if ($lastWatchedContentId == $lastOrderedProductAllContentId) {
                    return Content::find($lastOrderedProductAllContentId);
                }

                $nextWatchedContentId = $productAllContentIds[array_search($lastWatchedContentId,
                    $productAllContentIds) + 1];

                return Content::find($nextWatchedContentId);
            });
    }

    /**
     * Calculates costs of a product collection
     *
     * @param  Collection  $products
     *
     * @return mixed
     */
    protected function makeCostCollection(Collection $products)
    {
        $key = null;
        $cacheTags = ['product'];
        foreach ($products as $product) {
            $key .= $product->cacheKey().'-';
            $cacheTags[] = 'product_'.$product->id;
        }
        $key = 'product:makeCostCollection:'.md5($key);

        return Cache::tags($cacheTags)
            ->remember($key, config('constants.CACHE_60'), function () use ($products) {
                $costCollection = collect();
                foreach ($products as $product) {
                    if ($product->producttype_id == config('constants.PRODUCT_TYPE_CONFIGURABLE')) {
                        /** @var Collection $enableChildren */
                        $enableChildren = $product->children->where('enable',
                            1); // It is not query efficient to use scopeEnable
                        if ($enableChildren->count() == 1) {
                            $costArray = $enableChildren->first()
                                ->calculatePayablePrice();
                        } else {
                            $costArray = $product->calculatePayablePrice();
                        }
                    } else {
                        if ($product->producttype_id == config('constants.PRODUCT_TYPE_SELECTABLE')) {
                            $allChildren = $product->getAllChildren()
                                ->where('pivot.isDefault', 1);
                            $costArray = [];
                            $costArray['productDiscount'] = null;
                            $costArray['bonDiscount'] = null;
                            $costArray['costForCustomer'] = 0;
                            $costArray['cost'] = 0;
                            if (is_callable([$this, 'refreshPrice'])) {
                                $request = new Request();
                                $request->offsetSet('products', $allChildren->pluck('id')
                                    ->toArray());
                                $request->offsetSet('type', 'productSelection');
                                $costInfo = $this->refreshPrice($request, $product);
                                $costInfo = json_decode($costInfo);
                                $costArray['costForCustomer'] = $costInfo->costForCustomer;
                                $costArray['cost'] = $costInfo->cost;
                            }
//                    $costArray = $product->calculatePayablePrice();
                        } else {
                            $costArray = $product->calculatePayablePrice();
                        }
                    }

                    $costCollection->put($product->id, [
                        'cost' => $costArray['cost'],
                        'productDiscount' => $costArray['productDiscount'],
                        'bonDiscount' => $costArray['bonDiscount'],
                        'costForCustomer' => isset($costArray['costForCustomer']) ? $costArray['costForCustomer'] : 0,
                    ]);
                }

                return $costCollection;
            });
    }

    protected function makeProductCollection($productsId = null)
    {
        $key = '';
        $cacheTags = ['product', 'productCollection'];
        if (isset($productsId)) {
            foreach ($productsId as $product) {
                $cacheTags[] = 'product_'.$product;
                $key .= $product.'-';
            }
        }
        $key = 'product:makeProductCollection:'.$key;

        return Cache::tags($cacheTags)
            ->remember($key, config('constants.CACHE_60'), function () use ($productsId) {
                if (!isset($productsId)) {
                    $productsId = [];
                }

                $allProducts = Product::getProducts(0, 0, [], 'created_at', 'desc', $productsId)->get();

                $products = collect();
                foreach ($allProducts as $product) {
                    $products->push($product);
                }

                return $products;
            });
    }

    protected function haveSameFamily($products)
    {
        $key = null;
        $cacheTags = ['product'];
        foreach ($products as $product) {
            $key .= $product->cacheKey().'-';
            $cacheTags[] = 'product_'.$product->id;
        }
        $key = 'product:haveSameFamily:'.$key;

        return Cache::tags($cacheTags)
            ->remember($key, config('constants.CACHE_60'), function () use ($products) {
                $flag = true;
                foreach ($products as $key => $product) {
                    if (!(isset($products[$key + 1]))) {
                        continue;
                    }
                    if (!($product->grandParent != null && $products[$key + 1]->grandParent != null)) {
                        $flag = false;
                        break;
                    }
                    if ($product->grandParent->id != $products[$key + 1]->grandParent->id) {
                        $flag = false;
                        break;
                    }
                }

                return $flag;
            });
    }

    /**
     * @param  Repository  $videoDisk
     * @param                               $videoUrl
     * @param                               $videoPath
     * @param                               $size
     * @param  string  $caption
     * @param  string  $res
     * @param                               $videoExtension
     *
     * @return array
     */
    protected function makeIntroVideoFileStdClass(
        string $videoDisk,
        string $videoPath,
        string $videoExtension = null,
        $size = null,
        string $caption = null,
        string $res = null
    ): array {

        $basename = basename($videoPath);
        $arrayed = explode('/', $videoPath);
        $folder = isset(array_reverse($arrayed)[1]) ? array_reverse($arrayed)[1] : null;
        $filename = $folder.DIRECTORY_SEPARATOR.$basename;


        return [
            'uuid' => Str::uuid()->toString(),
            'disk' => $videoDisk,
            'fileName' => $filename,
            'size' => $size,
            'caption' => $caption,
            'res' => $res,
            'type' => 'video',
            'ext' => $videoExtension,
        ];
    }

    /**
     * @param  Repository  $thumbnailDisk
     * @param                               $thumbnailUrl
     * @param                               $thumbnailPath
     * @param                               $size
     * @param                               $caption
     * @param                               $res
     * @param                               $thumbnailExtension
     *
     * @return array
     */
    protected function makeVideoFileThumbnailStdClass(
        string $thumbnailDisk,
        string $thumbnailPath,
        string $thumbnailExtension = null,
        $size = null,
        $caption = null,
        string $res = null
    ): array {
        return [
            'uuid' => Str::uuid()->toString(),
            'disk' => $thumbnailDisk,
            'fileName' => basename($thumbnailPath),
            'size' => $size,
            'caption' => $caption,
            'res' => $res,
            'type' => 'thumbnail',
            'ext' => $thumbnailExtension,
        ];
    }

    /**
     * @param  array  $hqVideo
     *
     * @return array
     */
    protected function mekeIntroVideosArray(array $hqVideo): array
    {
        return [
            $hqVideo,
        ];
    }

    /**
     * @param  Product  $product
     *
     * @return Collection
     */
    private function makeAllChildrenSetCollection(Product $product): Collection
    {
        $allChildrenSets = collect();
        foreach ($product->getAllChildren(true, true) as $child) {
            $productSets = collect();
            foreach ($child->sets as $set) {
                $productSets->push([
                    'name' => $set->name,
                    'id' => $set->id,
                ]);
            }
            $allChildrenSets->push(['id' => $child->id, 'name' => $child->name, 'sets' => $child->sets]);
        }

        return $allChildrenSets;
    }

    /**
     * @param  array  $inputData
     * @param  Product  $product
     *
     * @return void
     * @throws FileNotFoundException
     */
    private function fillProductFromRequest(array $inputData, Product $product): void
    {
        $catalog = Arr::get($inputData, 'file');
        $images = Arr::has($inputData, 'image') ? [Arr::get($inputData, 'image')] : [];
        $wideImages = Arr::has($inputData, 'wide_image') ? [Arr::get($inputData, 'wide_image')] : [];
        $isFree = Arr::has($inputData, 'isFree');
        $hasInstalmentOption = Arr::has($inputData, 'has_instalment_option');
        $tagString = Arr::get($inputData, 'tags');
        $sampleContentString = Arr::get($inputData, 'sampleContents');
        $recommenderContentString = Arr::get($inputData, 'recommenderContents');
        $recommenderSetString = Arr::get($inputData, 'recommenderSets');
        $redirectUrl = Arr::get($inputData, 'redirectUrl', null);
        $redirectCode = Arr::get($inputData, 'redirectCode', null);

        if (isset($redirectUrl) && isset($redirectCode)) {
            $inputData['redirectUrl'] = [
                'url' => $redirectUrl,
                'code' => $redirectCode,
            ];
        }
        if (!$hasInstalmentOption) {
            $inputData['has_instalment_option'] = 0;
            $inputData['instalments'] = null;
        }

        $product->fill($inputData);

        $product->tags = convertTagStringToArray($tagString);

        $sampleContentsArray = convertTagStringToArray($sampleContentString);
        $introBlock = $product->blocks->first();
        $introBlockContents = optional(optional(optional($introBlock)->sets)->first())->getActiveContents2();
        $introBlockContentsArray = (isset($introBlockContents)) ? $introBlockContents->pluck('id')->toArray() : [];
        $product->sample_contents = array_unique(array_merge($sampleContentsArray, $introBlockContentsArray));

        $product->recommender_contents = [
            'contents' => convertTagStringToArray($recommenderContentString),
            'sets' => convertTagStringToArray($recommenderSetString),
        ];

        if ($this->strIsEmpty($product->discount)) {
            $product->discount = 0;
        }

        $product->isFree = $isFree;

        $product->intro_videos =
            $this->setIntroVideos(Arr::get($inputData, 'introVideo'), Arr::get($inputData, 'introVideoThumbnail'));

        //Storing product's catalog
        $storeFileResult = (isset($catalog)) ? $this->storeCatalogOfProduct($product, $catalog) : false;
        //ToDo : delete the file if it is an update

        //Storing product's image
        $storeImageResult = $this->storeImageOfProduct($product, $images);
        $storeWideImageResult = $this->storeImageOfProduct($product, $wideImages, 'wide_image');
        //ToDo : delete the file if it is an update
    }

    /**
     * @param $introVideo
     * @param $introVideoThumbnail
     *
     * @return Collection
     */
    private function setIntroVideos(?string $introVideo, ?string $introVideoThumbnail): Collection
    {
        $videos = null;
        if (isset($introVideo)) {
            $videos = $this->makeIntroVideos($introVideo);
        }

        $thumbnail = null;
        if (isset($introVideoThumbnail)) {
            $thumbnail = $this->makeIntroVideoThumbnail($introVideoThumbnail);
        }

        return $this->makeIntroVideoCollection($videos, $thumbnail);
    }

    /**
     * @param  array  $videos
     * @param  array  $thumbnail
     *
     * @return Collection
     */
    private function makeIntroVideoCollection(array $videos = null, array $thumbnail = null): Collection
    {
        $introVideos = collect();
        $introVideos->push([
            'video' => $videos,
            'thumbnail' => $thumbnail,
        ]);

        return $introVideos;
    }

    /**
     * Stores catalog file of the product
     *
     * @param  Product  $product
     *
     * @param  array  $files
     *
     * @return array
     * @throws FileNotFoundException
     */
    private function storeCatalogOfProduct(Product $product, $file): bool
    {
        try {
            $product->file = Uploader::put($file, config('disks.PRODUCT_CATALOG_MINIO'));
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * Stores image file of the product
     *
     * @param  Product  $product
     *
     * @param  array  $files
     *
     * @return array
     * @throws FileNotFoundException
     */
    private function storeImageOfProduct(Product $product, array $files, string $field = 'image'): array
    {
        $done = [];
        foreach ($files as $key => $file) {
            $extension = $file->getClientOriginalExtension();
            $fileName = basename($file->getClientOriginalName(), '.'.$extension).'_'.date('YmdHis').'.'.$extension;
            $done[$key] = false;
            $storeProcess = Uploader::put($file, config('disks.PRODUCT_IMAGE_MINIO'), $product, $fileName);
            if (!isset($storeProcess)) {
                continue;
            }

            $done[$key] = true;
            $product->$field = $storeProcess;
        }

        return $done;
    }

    /**
     * @param $product
     * @param $bonId
     * @param $bonDiscount
     * @param $bonPlus
     */
    private function attachBonToProduct(Product $product, int $bonId, int $bonDiscount, int $bonPlus): void
    {
        $bonQueryBuilder = $product->bons();

        if ($product->hasBon($bonId)) {
            $bonQueryBuilder->updateExistingPivot($bonId, [
                'discount' => $bonDiscount,
                'bonPlus' => $bonPlus,
            ]);
        } else {
            $bonQueryBuilder->attach($bonId, [
                'discount' => $bonDiscount,
                'bonPlus' => $bonPlus,
            ]);
        }
    }

    private function getGiftsOfRaheAbrisham(?Product $product): Collection
    {
        $key = 'order:determineGiftProducts:'.$product->cacheKey();
        return Cache::tags(['product', 'gifts_of_'.$product->id])->remember($key, config('constants.CACHE_600'),
            function () use ($product) {
                $giftProducts = collect();

                switch (optional($product)->id) {
                    case Product::RAHE_ABRISHAM99_FIZIK_RIYAZI :
                        !is_null($this->getGodarProduct(Product::GODAR_FIZIK_1400)) ? $giftProducts->push($this->getGodarProduct(Product::GODAR_FIZIK_1400)) : null;
                        !is_null($this->getArashProduct(Product::ARASH_FIZIK_1400_TOLOUYI)) ? $giftProducts->push($this->getArashProduct(Product::ARASH_FIZIK_1400_TOLOUYI)) : null;
                        break;

                    case Product::RAHE_ABRISHAM99_FIZIK_TAJROBI :
                        !is_null($this->getGodarProduct(Product::GODAR_FIZIK_99)) ? $giftProducts->push($this->getGodarProduct(Product::GODAR_FIZIK_99)) : null;
                        !is_null($this->getArashProduct(Product::ARASH_FIZIK_1400)) ? $giftProducts->push($this->getArashProduct(Product::ARASH_FIZIK_1400)) : null;
                        break;

                    case Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI:
                        !is_null($this->getGodarProduct(Product::GODAR_GOSASTE)) ? $giftProducts->push($this->getGodarProduct(Product::GODAR_GOSASTE)) : null;
                        !is_null($this->getGodarProduct(Product::GODAR_HENDESE)) ? $giftProducts->push($this->getGodarProduct(Product::GODAR_HENDESE)) : null;
                        !is_null($this->getGodarProduct(Product::GODAR_HESABAN)) ? $giftProducts->push($this->getGodarProduct(Product::GODAR_HESABAN)) : null;
                        !is_null($this->getArashProduct(Product::ARASH_RIYAZIYAT_RIYAZI_1400)) ? $giftProducts->push($this->getArashProduct(Product::ARASH_RIYAZIYAT_RIYAZI_1400)) : null;
                        break;

                    case Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI:
                        !is_null($this->getArashProduct(Product::ARASH_RIYAZI_TAJROBI_SABETI)) ? $giftProducts->push($this->getArashProduct(Product::ARASH_RIYAZI_TAJROBI_SABETI)) : null;
                        break;
                    case Product::RAHE_ABRISHAM99_SHIMI :
                        !is_null($this->getGodarProduct(Product::GODAR_SHIMI)) ? $giftProducts->push($this->getGodarProduct(Product::GODAR_SHIMI)) : null;
                        !is_null($this->getArashProduct(Product::ARASH_SHIMI_1400)) ? $giftProducts->push($this->getArashProduct(Product::ARASH_SHIMI_1400)) : null;
                        break;
                    case Product::RAHE_ABRISHAM99_ZIST :
                        !is_null($this->getGodarProduct(Product::GODAR_ZIST_1400)) ? $giftProducts->push($this->getGodarProduct(Product::GODAR_ZIST_1400)) : null;
                        !is_null($this->getArashProduct(Product::ARASH_ZIST_1400)) ? $giftProducts->push($this->getArashProduct(Product::ARASH_ZIST_1400)) : null;
                        break;
                    case Product::RAHE_ABRISHAM99_PACK_RIYAZI:
                        !is_null($this->getArashProduct(Product::ARASH_PACK_RITAZI_1400)) ? $giftProducts->push($this->getArashProduct(Product::ARASH_PACK_RITAZI_1400)) : null;
                        break;
                    case Product::RAHE_ABRISHAM99_PACK_TAJROBI:
                        !is_null($this->getArashProduct(Product::ARASH_PACK_TAJROBI_1400)) ? $giftProducts->push($this->getArashProduct(Product::ARASH_PACK_TAJROBI_1400)) : null;
                        break;
                }
                return $giftProducts;
            });
    }
}
