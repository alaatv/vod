<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventParticipantGroupsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('eventParticipantGroups')->delete();
        $data = [
            [
                'id'    => '1',
                'title' => 'دانش آموز',
            ],
            [
                'id'    => '2',
                'title' => 'فارغ التحصیل',
            ],
        ];

        DB::table('eventParticipantGroups')->insert($data); // Query Builder
    }
}
