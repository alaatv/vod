<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
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
                'id'           => '1',
                'name'         => 'adminPanel',
                'display_name' => 'پنل ادمین',
                'description'  => 'دسترسی به پنل ادمین',
            ],
            [
                'id'           => '2',
                'name'         => 'listPermission',
                'display_name' => 'مشاهده لیست دسترسی ها',
                'description'  => 'دسترسی به لیست دسترسی ها',
            ],
            [
                'id'           => '3',
                'name'         => 'insertPermission',
                'display_name' => 'درج دسترسی',
                'description'  => 'اجازه درج دسترسی',
            ],
            [
                'id'           => '4',
                'name'         => 'showPermission',
                'display_name' => 'نمایش دسترسی',
                'description'  => 'اجازه مشاهده اطلاعات دسترسی - پیش نیاز : مشاهده لیست دسترسی ها',
            ],
            [
                'id'           => '5',
                'name'         => 'editPermission',
                'display_name' => 'اصلاح دسترسی',
                'description'  => 'اجازه اصلاح دسترسی - پیش نیاز : مشاهده لیست دسترسی ها - نمایش دسترسی',
            ],
            [
                'id'           => '6',
                'name'         => 'removePermission',
                'display_name' => 'حذف دسترسی',
                'description'  => 'اجازه حذف دسترسی - پیش نیاز : لیست دسترسی ها',
            ],
        ];

        DB::table('permissions')
          ->insert($data); // Query Builder
    }
}
