<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 12/16/2018
 * Time: 12:11 PM
 */

namespace App\Classes\OrderProduct\RefinementProduct;

use App\Collection\ProductCollection;
use App\Models\Product;
use Exception;

class RefinementSelectable implements RefinementInterface
{
    private $selectedProductsIds;

    private $product;

    /**
     * RefinementSelectable constructor.
     *
     * @param  Product  $product
     * @param           $data
     *
     * @throws Exception
     */
    public function __construct(Product $product, $data)
    {
        if (!isset($data['products'])) {
            throw new Exception('products not set!');
        }
        $this->selectedProductsIds = $data['products'];
        $this->product = $product;
    }

    /**
     * @return ProductCollection|null
     */
    public function getProducts(): ?ProductCollection
    {
        /** @var ProductCollection $selectedProductsItems */
        $selectedProductsItems = Product::whereIn('id', $this->selectedProductsIds)
            ->get();

        $selectedProductsItems->keepOnlyParents();

        return $selectedProductsItems;
    }
}
