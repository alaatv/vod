<?php

namespace Database\Seeders\Orders;

use Database\Seeders\AllSeeder;

class AllOrdersSeeder extends AllSeeder
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
