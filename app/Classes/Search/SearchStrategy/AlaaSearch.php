<?php


namespace App\Classes\Search\SearchStrategy;


use App\Classes\Search\ContentSearch;
use App\Classes\Search\ContentsetSearch;
use App\Classes\Search\LiveDescriptionSearch;
use App\Classes\Search\ProductSearch;
use App\Http\Resources\ContentInSearch;
use App\Http\Resources\ProductInBlock;
use App\Http\Resources\SetInSearch;
use App\Models\Contenttype;
use Illuminate\Support\Arr;

class AlaaSearch implements SearchInterface
{
    private $contentSearch;
    private $setSearch;
    private $productSearch;

    public function __construct(ContentSearch $contentSearch, ContentsetSearch $setSearch, ProductSearch $productSearch)
    {
        $this->contentSearch = $contentSearch;
        $this->setSearch = $setSearch;
        $this->productSearch = $productSearch;
    }

    public function search(array $request): array
    {
        Arr::set($request, 'free', Arr::get($request, 'free', [1]));
        $contentTypes = array_filter(Arr::get($request, 'contentType', Contenttype::video()));
        $contentOnly = Arr::get($request, 'contentOnly', false);
        $tags = (array) Arr::get($request, 'tags');

        $filters = $request;

        $filters['notFileExt'] = '.mp3';
        $filters['active'] = 1;
        $filters['display'] = 1;
        $filters['enable'] = 1;
        $filters['doesntHaveGrand'] = 1;


        $params = compact('filters', 'contentTypes');
        $videos = $this->contentSearch->get($params);
        $products = $this->productSearch->get($filters);
        $sets = $this->setSearch->get($filters);


        $videos = $videos->get('video');
        $videos = isset($videos) && $videos->count() > 0 ? ContentInSearch::nestedCollection($videos) : null;


        $sets = !$contentOnly && isset($sets) && $sets->count() > 0 ? SetInSearch::nestedCollection($sets) : null;


        $products =
            !$contentOnly && isset($products) && $products->count() > 0 ? ProductInBlock::nestedCollection($products) : null;

        return [
            'data' =>
                [
                    'videos' => $videos,
                    'products' => $products,
                    'sets' => $sets,
                    'tags' => empty($tags) ? null : $tags,
                ],
        ];
    }

    public function liveDescriptionInit()
    {
        return new LiveDescriptionSearch();
    }

    public function searchLiveDescriptions(array $data, int $length = 5)
    {
        $liveDescriptionSearch = new LiveDescriptionSearch();
        return $liveDescriptionSearch->setNumberOfItemInEachPage($length)->get($data);
    }
}
