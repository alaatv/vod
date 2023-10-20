<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-16
 * Time: 12:40
 */

namespace App\Classes\Search\Filters;

use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;

class ContentType extends FilterAbstract
{
    protected $attribute = 'contenttype_id';

    protected $lookUp = [
        'video' => Content::CONTENT_TYPE_VIDEO,
        'pamphlet' => Content::CONTENT_TYPE_PAMPHLET,
        'article' => Content::CONTENT_TYPE_ARTICLE,
    ];

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        $value = $this->getSearchValue($value);

        return $builder->whereIn($this->attribute, $value);
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function getSearchValue($value): array
    {
        $searchValue = [];
        foreach ($value as $v) {
            array_push($searchValue, $this->lookUp[$v]);
        }

        return $searchValue;
    }
}
