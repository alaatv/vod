<?php

namespace Database\Seeders\Other;

use Database\Seeders\AllSeeder;

class AllOtherSeeder extends AllSeeder
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
