<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ticketStatuses')->delete();
        $data = [
            [
                'id'             => '1',
                'title'          => 'پاسخ داده نشده',
                'name'           => 'unanswered',
            ],
            [
                'id'             => '2',
                'title'          => 'در حال بررسی',
                'name'           => 'pending',
            ],
            [
                'id'             => '3',
                'title'          => 'پاسخ داده شده',
                'name'           => 'answered',
            ],
            [
                'id'             => '4',
                'title'          => 'بسته شده',
                'name'           => 'closed',
            ],
        ];

        DB::table('ticketStatuses')->insert($data); // Query Builder
    }
}
