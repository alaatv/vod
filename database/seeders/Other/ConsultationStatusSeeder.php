<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsultationStatusSeeder extends Seeder
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
                'id' => '1',
                'name' => 'active',
                'displayName' => 'فعال',
                'description' => 'قابل مشاهده برای کاربران',
            ],
            [
                'id' => '2',
                'name' => 'inactive',
                'displayName' => 'غیر فعال',
                'description' => 'غیر قابل مشاهده برای کاربران',
            ],
        ];

        DB::table('consultationstatuses')
            ->insert($data); // Query Builder
    }
}
