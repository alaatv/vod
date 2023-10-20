<?php

namespace Database\Seeders\Product;

use App\Models\Productinterrelation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductInterrelationSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Productinterrelation::truncate();
        $data = [
            [
                'id'          => '1',
                'name'        => 'gift',
                'displayName' => 'هدیه',
                'description' => 'به طوری قرارداری دومی هدیه اولی می باشد',
            ],
        ];
        DB::table('productinterrelations')
          ->insert($data); // Query Builder
    }
}
