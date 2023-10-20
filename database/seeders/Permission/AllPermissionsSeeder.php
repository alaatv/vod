<?php

namespace Database\Seeders\Permission;

use Database\Seeders\AllSeeder;

class AllPermissionsSeeder extends AllSeeder
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
