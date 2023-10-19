<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use src\Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TicketActionsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ticketActions')
            ->delete();
        $data = [
            [
                'id'             => '1',
                'title'          => 'ثبت تیکت',
            ],
            [
                'id'             => '2',
                'title'          => 'اصلاح اطلاعات تیکت',
            ],
            [
                'id'             => '3',
                'title'          => 'حذف تیکت',
            ],
            [
                'id'             => '4',
                'title'          => 'تغییر عنوان تیکت',
            ],
            [
                'id'             => '5',
                'title'          => 'تغییر وضعیت تیکت',
            ],
            [
                'id'             => '6',
                'title'          => 'تغییر دپارتمان تیکت',
            ],
            [
                'id'             => '7',
                'title'          => 'تغییر اولویت تیکت',
            ],
            [
                'id'             => '8',
                'title'          => 'تغییر ثبت کننده کاربر تیکت',
            ],
            [
                'id'             => '9',
                'title'          => 'تغییر آیتم محصول مرتبط با تیکت',
            ],
            [
                'id'             => '10',
                'title'          => 'ثبت پیام برای تیکت',
            ],
            [
                'id'             => '11',
                'title'          => 'ثبت پیام خصوصی برای تیکت',
            ],
            [
                'id'             => '12',
                'title'          => 'اصلاح اطلاعات پیام تیکت',
            ],
            [
                'id'             => '13',
                'title'          => 'حذف پیام تیکت',
            ],
            [
                'id'             => '14',
                'title'          => 'تغییر متن یک پیام تیکت',
            ],
            [
                'id'             => '15',
                'title'          => 'انتقال پیام تیکت به تیکت دیگر',
            ],
            [
                'id'             => '16',
                'title'          => 'تغییر کاربر ثبت کننده پیام تیکت',
            ],
            [
                'id'             => '17',
                'title'          => 'تغییر فایل عکس پیام تیکت',
            ],
            [
                'id'             => '18',
                'title'          => 'تغییر فایل صدای تیکت',
            ],
        ];


        DB::table('ticketActions')->insert($data); // Query Builder
    }
}
