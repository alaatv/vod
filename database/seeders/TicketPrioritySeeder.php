<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use src\Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ticketPriorities')
            ->delete();
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
