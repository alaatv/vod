<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialCategoriesSeeder extends Seeder
{

    public function run()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'کتاب آنلاین گویای متوسطه دوم',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'سه آ',
                'created_at' => now(),
            ],
        ];
        DB::table('financial_categories')
            ->insert($data);
    }
}
