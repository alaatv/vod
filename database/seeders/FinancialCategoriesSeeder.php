<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialCategoriesSeeder extends AlaaSeeder
{

    protected function setData(): void
    {
        $this->data = [
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
    }

    protected function setTable(): void
    {
        $this->table = 'financial_categories';
    }
}
