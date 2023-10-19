<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SMSProvidersSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_providers')->delete();
        $data = [
            ['id' => '1', 'number' => '2162013', 'cost' => '13', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '2', 'number' => '100062013', 'cost' => '13', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '3', 'number' => '500010409232', 'cost' => '85', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '4', 'number' => '10000066009232', 'cost' => '100', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '5', 'number' => '2000505', 'cost' => '0', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '6', 'number' => '500010707', 'cost' => '0', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '7', 'number' => '50009589', 'cost' => '0', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '8', 'number' => '10000385', 'cost' => '0', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '9', 'number' => '100020400', 'cost' => '100', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '10', 'number' => '5000125475', 'cost' => '85', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '11', 'number' => '1000958', 'cost' => '100', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '12', 'number' => '5000189', 'cost' => '85', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '13', 'number' => '5000958', 'cost' => '85', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
            ['id' => '14', 'number' => '3000505', 'cost' => '85', 'description' => null, 'created_at' => '2021-03-06 12:53:50', 'updated_at' => '2021-03-06 12:53:50', 'deleted_at' => null],
        ];

        DB::table('sms_providers')
          ->insert($data); // Query Builder
    }
}
