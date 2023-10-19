<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 12/23/2018
 * Time: 5:19 PM
 */

namespace App\Classes\OrderProduct\RefinementProduct;

use App\Collection\ProductCollection;

interface RefinementInterface
{
    /**
     * @return ProductCollection|null
     */
    public function getProducts(): ?ProductCollection;
}
