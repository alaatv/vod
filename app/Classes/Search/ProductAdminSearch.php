<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-17
 * Time: 18:41
 */

namespace App\Classes\Search;

use App\Classes\Search\{Filters\Tags, Tag\ProductTagManagerViaApi};
use App\Models\Product;
use Illuminate\Database\Eloquent\{Builder};

class ProductAdminSearch extends SearchAbstract
{
    protected $model = Product::class;

    protected $pageName = 'productPage';

    protected $numberOfItemInEachPage = 5;

    protected $validFilters = [
        'id',
        'name',
        'tags',
        'active',
        'doesntHaveGrand',

        'short_description',
        'long_description',
        'is_free',
        'enable',
        'display',
        'product_type_id',
        'category',
    ];

    /**
     * @param  array  $filters
     *
     * @return mixed
     */
    protected function apply(array $filters)
    {
        $this->pageNum = $this->setPageNum($filters);

        $query = $this->applyDecoratorsFromFiltersArray($filters, $this->model->newQuery());

        return $this->getResults($query)
            ->appends($filters);
    }

    /**
     * @param  Builder  $query
     *
     * @return mixed
     */
    protected function getResults(Builder $query)
    {
        /** @var Product $query */
        $result = $query
            ->orderBy('created_at', 'desc')
            ->paginate($this->numberOfItemInEachPage, ['*'], $this->pageName, $this->pageNum);

        return $result;
    }

    /**
     * @param $decorator
     *
     * @return mixed
     */
    protected function setupDecorator($decorator)
    {
        $decorator = (new $decorator());
        if ($decorator instanceof Tags) {
            $decorator->setTagManager(new ProductTagManagerViaApi());
        }

        return $decorator;
    }
}
