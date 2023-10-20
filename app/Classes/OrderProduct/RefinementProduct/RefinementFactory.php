<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 12/16/2018
 * Time: 11:53 AM
 */

namespace App\Classes\OrderProduct\RefinementProduct;

use App\Models\Product;
use Mockery\Exception;

class RefinementFactory
{
    private $product;

    private $data;

    public function __construct(Product $product, $data = null)
    {
        $this->product = $product;
        $this->data = $data;
    }

    public function getRefinementClass()
    {
        $typeName = $this->product->producttype->name;
        $className = __NAMESPACE__.'\Refinement'.ucfirst($typeName);
        if (class_exists($className)) {
            return new $className($this->product, $this->data);
        }
        throw new Exception('Type Name {'.$typeName.'} not found.');
    }
}
