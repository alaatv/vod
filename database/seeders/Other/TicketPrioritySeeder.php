<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketPrioritySeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            [
                'id'             => '1',
                'title'          => 'کم',
            ],
            [
                'id'             => '2',
                'title'          => 'متوسط',
            ],
            [
                'id'             => '3',
                'title'          => 'فوری',
            ],
            [
                'id'             => '4',
                'title'          => 'بحرانی',
            ],
        ];

        DB::table('ticketPriorities')->insert($data); // Query Builder
    }
}
