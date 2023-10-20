<?php

namespace Database\Seeders\Product;

use Database\Seeders\AllSeeder;

class AllProductsSeeder extends AllSeeder
{
    protected function getDirectory(): string
    {
        return __DIR__;
    }

    protected function getDNameSpace(): string
    {
        return __NAMESPACE__;
    }
}
