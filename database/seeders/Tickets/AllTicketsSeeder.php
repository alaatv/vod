<?php

namespace Database\Seeders\Tickets;

use Database\Seeders\AllSeeder;

class AllTicketsSeeder extends AllSeeder
{
    protected function getDirectory():string
    {
        return __DIR__;
    }
    protected function getDNameSpace():string
    {
        return __NAMESPACE__;
    }
}
