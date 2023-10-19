<?php

namespace Database\Seeders;

class ReportTypeSeeder extends AlaaSeeder
{
    protected function setData(): void
    {
        $this->data = [
            [
                'id' => 1,
                'title' => 'audit',
                'title_display_name' => 'حسابرسی',
                'created_at' => now()
            ],
            [
                'id' => 2,
                'title' => 'customers',
                'title_display_name' =>  'مشتریان',
                'created_at' => now()
            ],
            [
                'id' => 3,
                'title' => 'orderproducts',
                'title_display_name' =>  'محصولات سفارش',
                'created_at' => now()
            ],
            [
                'id' => 4,
                'title' => 'customersInfo',
                'title_display_name' =>  'اطلاعات مشتریان',
                'created_at' => now()
            ],
            [
                'id' => 5,
                'title' => 'transactions',
                'title_display_name' =>  'انتقالات',
                'created_at' => now()
            ],
            [
                'id' => 6,
                'title' => 'users',
                'title_display_name' =>  'کاربران',
                'created_at' => now()
            ],
            [
                'id' => 7,
                'title' => 'raheAbrishamCustomers',
                'title_display_name' =>  'مشتریان راه ابریشم',
                'created_at' => now()
            ],
        ];
    }

    protected function setTable(): void
    {
        $this->table = 'report_types';
    }
}
