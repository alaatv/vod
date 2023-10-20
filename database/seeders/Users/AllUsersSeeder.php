<?php

namespace Database\Seeders\Users;

use Database\Seeders\AllSeeder;

class AllUsersSeeder extends AllSeeder
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
