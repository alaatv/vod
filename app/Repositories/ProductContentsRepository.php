<?php


namespace App\Repositories;


use App\Models\Comment;
use App\Models\Content;
use App\Models\Product;
use Carbon\Carbon;

class ProductContentsRepository
{
    public static function productInitQuery()
    {
        return Product::query();
    }

    public static function contents(
        $product,
        $productId,
        $contentType,
        $paginate = null,
        $search = null,
        $setSearch = null
    ) {
        $contentIds = self::contentIds($product, $productId, $contentType, $search, $setSearch);
        $contentsBuilder = Content::whereIn('id', $contentIds);
        return $paginate ? $contentsBuilder->paginate($paginate)->withQueryString() : $contentsBuilder->get();
    }

    public static function contentIds($product, $productId, $contentType = [], $search = null, $setSearch = null)
    {
        $product = $product->where('id', $productId)->first();
        $contentIds = [];
        $sets = $product->sets();
        if (!is_null($setSearch)) {
            $sets = $sets->search($setSearch);
        }
        $sets = $sets->where('enable', true)->with([
            'activeContents' => function ($query) use ($contentType, $search) {
                if (!is_null($search)) {
                    $query->search($search);
                }
                if (!empty($contentType)) {
                    $query->whereIn('contenttype_id', $contentType);
                }
                return $query;
            }
        ])->get();
        foreach ($sets as $set) {
            $contentIds = array_merge($contentIds, $set->activeContents->pluck('id')->toArray());
        }
        return $contentIds;
    }

    public static function comments(
        $product,
        $user,
        $productId,
        $contentType,
        $commentNameSearch = null,
        $setSearch = null,
        $startDate = null,
        $endDate = null,
        $paginate = 15
    ) {
        $contentIds = self::contentIds($product, $productId, $contentType, null, $setSearch);
        $comments =
            Comment::where('author_id', $user->id)
                ->whereIn('commentable_id', $contentIds)
                ->where('commentable_type', get_class(new Content()));

        if (!is_null($commentNameSearch)) {
            $comments = $comments->search($commentNameSearch);
        }
        if (!is_null($startDate)) {
            $comments = $comments->where('created_at', '>=', Carbon::parse($startDate));
        }
        if (!is_null($endDate)) {
            $comments = $comments->where('created_at', '<=', Carbon::parse($endDate));
        }

        return $comments->paginate($paginate)->withQueryString();
    }
}
