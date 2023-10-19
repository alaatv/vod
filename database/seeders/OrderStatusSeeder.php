<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orderstatuses')
          ->delete();

        $data = [
            [
                'id'          => '1',
                'name'        => 'open',
                'displayName' => 'باز',
                'description' => 'این سفارش توسط کاربر باز شده است و در حال حاضر باز و قابل استفاده می باشد',
            ],
            [
                'id'          => '2',
                'name'        => 'closed',
                'displayName' => 'ثبت نهایی',
                'description' => 'مراحل این سفارش با موفقیت به اتمام رسیده و بسته شده است',
            ],
            [
                'id'          => '3',
                'name'        => 'canceled',
                'displayName' => 'لغو شده',
                'description' => 'این سفارش توسط کاربر لغو شده است',
            ],
            [
                'id'          => '4',
                'name'        => 'openByAdmin',
                'displayName' => 'باز مدیریتی',
                'description' => 'سفارش توسط مسئول سایت باز شده است',
            ],
            [
                'id'          => '5',
                'name'        => 'posted',
                'displayName' => 'تحویل پست شده',
                'description' => 'سفارش تحویل پست داده شده',
            ],
            [
                'id'          => '6',
                'name'        => 'refunded',
                'displayName' => 'بازگشت هزینه',
                'description' => 'هزینه ی سفارش به دلایلی مانند لغو سفارش از طرف مشتری بازگردانده شده است',
            ],
            [
                'id'          => '7',
                'name'        => 'readyToPost',
                'displayName' => 'آماده به ارسال',
                'description' => 'مرسوله ی سفارش آماده برای ارسال می باشد',
            ],
            [
                'id'          => '8',
                'name'        => 'openDonate',
                'displayName' => 'سفارش باز کمک مالی',
                'description' => 'سفارشی که برای کمک مالی باز شده است',
            ],
            [
                'id'          => '9',
                'name'        => 'pending',
                'displayName' => 'در حال بررسی',
                'description' => 'سفارش در حال بررسی توسط مسئولین سایت می باشد',
            ],
            [
                'id'          => '10',
                'name'        => 'blocked',
                'displayName' => 'مسدود شده',
                'description' => 'سفارش به دلیل غیر مجاز بودن مسدود شده است',
            ],
            [
                'id'          => '11',
                'name'        => 'open3a',
                'displayName' => 'سفارش باز سه آ',
                'description' => 'سفارشی که برای خرید سه آ باز شده است',
            ],
        ];

        DB::table('orderstatuses')
          ->insert($data); // Query Builder

    }
}
