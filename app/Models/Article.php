<?php

namespace App\Models;


class Article extends BaseModel
{
    //    use Searchable;

    protected $fillable = [
        'user_id',
        'articlecategory_id',
        'order',
        'title',
        'keyword',
        'brief',
        'body',
        'image',
    ];

    public static function recentArticles($number)
    {
        return Article::take($number)
            ->orderBy('created_at', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articlecategory()
    {
        return $this->belongsTo(Articlecategory::class);
    }

    public function sameCategoryArticles($number)
    {
        return Article::where('articlecategory_id', $this->articlecategory_id)
            ->where('id', '<>', $this->id)
            ->orderBy('created_at', 'desc')
            ->take($number);
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'articles_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        unset($array['image']);

        return $array;
    }
}
