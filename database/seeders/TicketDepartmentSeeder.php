<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use src\Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TicketDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ticketDepartments')
            ->delete();
        $data = [
            [
                'id'             => '1',
                'title'          => 'آموزش',
            ],
        ];

        DB::table('ticketDepartments')->insert($data); // Query Builder
    }
}
