<?php

namespace Database\Seeders;

use App\Orderstatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddOpen3aOrderStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'open3a',
                'displayName' => 'سفارش باز سه آ',
                'description' => 'سفارشی که برای خرید سه آ باز شده است',
            ],
        ];

        DB::table('orderstatuses')
            ->insert($data);
    }
}
