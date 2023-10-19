<?php


namespace App\Classes\Search\SearchStrategy;


//use App\Classes\TagsGroup;
use App\Http\Resources\ContentInSearch;
use App\Http\Resources\ProductInBlock;
use App\Http\Resources\SetInIndex;
use App\Models\Content;
use App\Models\Product;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

//TODO:// refactor
class SearchiaSearch implements SearchInterface
{
    private const RETRIEVE_SIZE = 20;
    public const HEADERS = [
        'content-type' => 'application/JSON',
        'apikey' => 'iQohNtYGIIbt1w3'
    ];
    public const INDEX = 'alaatv';
    public const MAIN_URL = 'https://searchia.ir/api/index/';
    public const TAG_MAP = [
        1 => 'educational_system_tags',
        2 => 'grade_tags',
        3 => 'major_tags',
        4 => 'lesson_tags',
        5 => 'teacher_tags',
        6 => 'other_tags',
    ];

    public function search(array $search): array
    {
        $response = $this->getResponse($search);
        $response = $response ? $this->retrieveFromDB($response) : [];
        [$products, $sets, $videos] = $this->makeResponseCollections($response);


        return [
            'data' =>
                [
                    'videos' => $videos,
                    'products' => $products,
                    'sets' => $sets,
                    'tags' => Arr::get($search, 'tags'),
                ],
        ];
    }

    private function getResponse(array $request): array
    {
        $query = $this->makeQuery($request);
        $rawResponse = Http::withHeaders(self::HEADERS)
            ->get($query)
            ->body();
        return $this->parseResponse($rawResponse);
    }

    private function makeQuery(array $search): string
    {
        $query = Arr::get($search, 'q', '');
        $urlEncodeQuery = '?query='.urlencode($query);

        $filters = $this->makeFilters($search);
        $from = $this->setFrom($search);
        $size = $this->setSize($search);

        return self::MAIN_URL
            .self::INDEX
            .$urlEncodeQuery
            .$from
            .$size
            .$filters;
    }

    private function makeFilters(array $request): string
    {

        $filtersPrefix = '&filters=';
        $tagFilters = $this->makeTagFilters($request);
        $typeFilters = $this->makeTypeFilters($request);

        return $filtersPrefix.$tagFilters.$typeFilters;
    }

    private function makeTagFilters(array $request): string
    {
        $filters = '';
        $tags_groups = $this->getTagGroups($request);
        if (!$tags_groups) {
            return $filters;
        }
        $forCounter = count($tags_groups);
        foreach ($tags_groups as $key => $tags) {
            $filters .= '(';
            foreach ($tags as $tagIndex => $tag) {
                $filters .= ($tagIndex == count($tags) - 1) ? "$key:$tag" : "$key:$tag or ";
            }
            $filters .= (--$forCounter == 0) ? ')' : ') and ';
        }
        return $filters;
    }

    private function getTagGroups(array $request): array
    {
        $result = [];
        $tags = isset($request['tags']) ?: [];
        $tags_groups = (new TagsGroup($tags))->getTagsGroup()->toArray();
        $tags_groups = [];
        foreach ($tags_groups as $key => $tagArray) {
            $result[self::TAG_MAP[$key]] = $tagArray;
        }
        return $result;
    }

    private function makeTypeFilters(array $request): string
    {
        if (isset($request['videoPage'])) {
            $type = Arr::last(explode("\\", Content::class));
            return "and (type:$type)";
        }
        if (isset($request['productPage'])) {
            $type = Arr::last(explode("\\", Product::class));
            return "and (type:$type)";
        }
        return '';
    }

    private function setFrom(array $request): string
    {
        if (isset($request['videoPage'])) {
            $from = (($request['videoPage'] - 1) * self::RETRIEVE_SIZE) - (isset($request['lastVideoCount']) ?: 0);
            return '&from='.$from;
        }
        if (isset($request['productPage'])) {
            $from = (($request['productPage'] - 1) * self::RETRIEVE_SIZE) - (isset($request['lastProductCount']) ?: 0);
            return '&from='.$from;
        }
        return '';
    }

    private function setSize(array $search): string
    {
        $size = self::RETRIEVE_SIZE;
        if (!Arr::get($search, 'videoPage') && !Arr::get($search, 'productPage')) {
            $size *= 3;
        }
        return "&size=$size";
    }

    private function parseResponse(string $rawResponse): array
    {
        $parsedResponse = [];
        $hits = $this->getHits($rawResponse);
        foreach ($hits as $hit) {
            [$type, $id] = $this->fillParsedResponse($hit);
            if ($type) {
                $parsedResponse[$type][] = $id;
            }
        }
        return $parsedResponse;
    }

    private function getHits(string $rawResponse): array
    {
        $jsonDecoded = json_decode($rawResponse);
        return $jsonDecoded?->entity?->results ?? [];
    }

    private function fillParsedResponse($hit)
    {
        try {
            $data = $hit->source;
            $type = $data->type;
            $id = $data->id;
            return [$type, $id];
        } catch (Exception $exception) {
            // continue
        }
    }

    private function retrieveFromDB($response): array
    {
        $data = [];
        foreach ($response as $type => $ids) {
            $model = "App\\".$type;
            $data[$type] = $model::whereIn('id', $ids)->paginate();
        }
        return $data;
    }

    private function makeResponseCollections(array $response): array
    {
        $products = isset($response['Product']) && $response['Product']->count() > 0 ? ProductInBlock::collection($response['Product']) : null;
        $sets = isset($response['Contentset']) && $response['Contentset']->count() > 0 ? SetInIndex::collection($response['Contentset']) : null;
        $videos = isset($response['Content']) && $response['Content']->count() > 0 ? ContentInSearch::collection($response['Content']) : null;

        return [$products, $sets, $videos];
    }
}
