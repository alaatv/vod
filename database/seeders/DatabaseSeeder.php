<?php

namespace Database\Seeders;

use Database\Seeders\Orders\AllOrdersSeeder;
use Database\Seeders\Other\AllOtherSeeder;
use Database\Seeders\Permission\AllPermissionsSeeder;
use Database\Seeders\Product\AllProductsSeeder;
use Database\Seeders\ReferralCode\AllReferralCodesSeeder;
use Database\Seeders\Tickets\AllTicketsSeeder;
use Database\Seeders\Users\AllUsersSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $this->truncateTableAll();
        $this->call([
            AllUsersSeeder::class,
            AllPermissionsSeeder::class,
            AllProductsSeeder::class,
            AllReferralCodesSeeder::class,
            AllOrdersSeeder::class,
            AllTicketsSeeder::class,
            AllOtherSeeder::class
        ]);
        Schema::enableForeignKeyConstraints();
    }

    protected static function truncateTableAll(array $skip = [])
    {
        foreach (Schema::getConnection()
                     ->getDoctrineSchemaManager()
                     ->listTableNames() as $name) {

            if ($name == 'migrations' || in_array($name, $skip)) {
                continue;
            }
            DB::table($name)->truncate();
        }
    }
}
