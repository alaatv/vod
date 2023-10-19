<?php


namespace App\Classes;


use App\Classes\Search\SearchStrategy\SearchFactory;
use App\Models\Content;
use App\Traits\APIRequestCommon;
use stdClass;

class RecommendedItemsGeneratorForContent
{
    use APIRequestCommon;

    public const ITEM_TYPE_CONTENT = 'content';
    public const ITEM_TYPE_PRODUCT = 'product';
    public const ITEM_TYPE_SET = 'set';
    public const NUMBER_OF_RECOMMENDED_PRODUCTS = 3;

    protected $content;
    protected $tags;

    /**
     * RecommendedItemsGenerator constructor.
     *
     * @param  Content  $content
     */
    public function __construct(Content $content)
    {
        $this->content = $content;
        $this->tags = $content->retrievingTags();
    }

    public function fetch(): array
    {
        $fetchedRecommendedItems = $this->getRecommendedItems($this->tags);
        $products = $this->getRecommendedProducts($fetchedRecommendedItems);
        $sets = $this->getRecommendedSets($fetchedRecommendedItems);
        $videos = $this->getRecommendedVideos($fetchedRecommendedItems);

        $products = $this->prepareRecommendedProducts($products);
        $sets = $this->prepareRecommendedSets($sets);
        $videos = $this->prepareRecommendedVideos($videos);

        return array_merge($products, $videos, $sets);
    }

    private function getRecommendedItems(array $tags): stdClass
    {
        $result = SearchFactory::factory()->search($tags);
        $result = json_encode($result['data']);

        return json_decode($result);
    }

    private function getRecommendedProducts($result): array
    {
        return optional(optional($result)->products)->data ?? [];
    }

    private function getRecommendedSets($result): array
    {
        return optional(optional($result)->sets)->data ?? [];
    }

    private function getRecommendedVideos($result): array
    {
        return optional(optional($result)->videos)->data ?? [];
    }

    private function prepareRecommendedProducts(array $products): array
    {
        $recommendedProducts = $this->content->recommended_products;
        $recommendedProducts = $recommendedProducts->toArray();
        $recommendedProducts = json_encode($recommendedProducts);
        $totalProducts = json_decode($recommendedProducts);
        shuffle($totalProducts);
        $totalProducts = array_slice($totalProducts, 0, self::NUMBER_OF_RECOMMENDED_PRODUCTS);

        $totalProductsCount = count($totalProducts);
        if ($totalProductsCount < self::NUMBER_OF_RECOMMENDED_PRODUCTS) {
            $productsOfThisContent = $this->content->activeProducts();
            $productsOfThisContent = $productsOfThisContent->toArray();
            $productsOfThisContent = json_encode($productsOfThisContent);
            $productsOfThisContent = json_decode($productsOfThisContent);
            $productsOfThisContent =
                array_slice($productsOfThisContent, 0, (self::NUMBER_OF_RECOMMENDED_PRODUCTS - $totalProductsCount));
            $totalProducts = array_merge($totalProducts, $productsOfThisContent);
        }

        if (count($totalProducts) == 0) {
            $totalProducts = array_slice($products, 0, 3);
        }

        array_walk($totalProducts, function (&$val) {
            $val->item_type = self::ITEM_TYPE_PRODUCT;
        });

        return $totalProducts;
    }

    private function prepareRecommendedSets(array $sets): array
    {
        $sameContentKey = null;
        array_walk($sets, function (&$val, $key) use (&$sameContentKey) {
            $val->item_type = self::ITEM_TYPE_SET;
            if (isset($val->id) && $val->id == $this->content->contentset_id) {
                $sameContentKey = $key;
            }
        });
        if (isset($sameContentKey)) {
            unset($sets[$sameContentKey]);
            $sets = array_values($sets);
        }

        return array_slice($sets, 0, 1);
    }

    private function prepareRecommendedVideos(array $videos): array
    {
        $sameContentKey = null;
        array_walk($videos, function (&$val, $key) use (&$sameContentKey) {
            $val->item_type = self::ITEM_TYPE_CONTENT;
            if (isset($val->id) && $val->id == $this->content->id) {
                $sameContentKey = $key;
            }
        });
        if (isset($sameContentKey)) {
            unset($videos[$sameContentKey]);
            $videos = array_values($videos);
        }

        return array_slice($videos, 0, 2);
    }
}
