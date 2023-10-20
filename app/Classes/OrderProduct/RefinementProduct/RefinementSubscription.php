<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 12/16/2018
 * Time: 12:00 PM
 */

namespace App\Classes\OrderProduct\RefinementProduct;

use App\Collection\ProductCollection;
use App\Models\Product;

class RefinementSubscription implements RefinementInterface
{
    private $product;

    public function __construct(Product $product, $data)
    {
        $this->product = $product;
    }

    public function getProducts(): ?ProductCollection
    {
        $simpleProduct = new ProductCollection();
        $simpleProduct->push($this->product);

        return $simpleProduct;
    }
}
