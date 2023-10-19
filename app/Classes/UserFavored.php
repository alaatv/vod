<?php

namespace App\Classes;

use App\Http\Resources\ContentTimePointWeb;
use App\Http\Resources\ContentWithFavoredTimePoints;
use App\Http\Resources\Product;
use App\Http\Resources\SetInIndex;
use App\Models\User;
use App\Repositories\ProductContentsRepository;

class UserFavored
{
    public int $limit;
    public ?int $productId = null;
    public ?string $search = null;
    public ?string $contentSetTitle = null;
    public ?array $contentTypeIds = null;

    public function __construct(public User $user, public string $type)
    {
    }

    public function setLimitForPaginate(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function setProductId(int|null $productId): static
    {
        $this->productId = $productId;
        return $this;
    }

    public function setSearch(string|null $search): static
    {
        $this->search = $search;
        return $this;
    }

    public function setContentSetTitle(string|null $contentSetTitle): static
    {
        $this->contentSetTitle = $contentSetTitle;
        return $this;
    }

    public function setContentTypeIds(array|null $contentTypeId): static
    {
        $this->contentTypeId = $contentTypeId;
        return $this;
    }

    public function get()
    {
        $data = $this->{$this->type}();
        return $this->resource()::collection($data);
    }

    public function resource()
    {
        return match ($this->type) {
            'content' => ContentWithFavoredTimePoints::class,
            'product' => Product::class,
            'set' => SetInIndex::class,
            'timePoint' => ContentTimePointWeb::class,
        };
    }

    private function content()
    {
        $data = $this->user->getTotalActiveFavoredContentsWithoutCache($this->search, $this->contentSetTitle);
        $data = match (true) {
            isset($this->productId) => $this->setProductIdAsFilter($data),
            isset($this->contentTypeId) => $data->whereIn('contenttype_id', $this->contentTypeId),
            default => $data
        };
        return $data->paginate($this->limit)->withQueryString();
    }

    private function setProductIdAsFilter($data)
    {
        $product = ProductContentsRepository::productInitQuery();
        $contentIds = ProductContentsRepository::contentIds($product, $this->productId, $this->contentTypeId ?? []);
        return $data->whereIn('id', $contentIds);
    }

    private function set()
    {
        return $this->user->getActiveFavoredSets()->paginate($this->limit)->withQueryString();
    }

    private function product()
    {
        return $this->user->getActiveFavoredProducts()->paginate($this->limit)->withQueryString();
    }

    private function timePoint()
    {
        return $this->user->getActiveFavoredContentTimepoints()->paginate($this->limit)->withQueryString();
    }
}
