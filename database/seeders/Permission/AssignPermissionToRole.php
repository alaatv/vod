<?php

namespace Database\Seeders\Permission;

use Illuminate\Database\Seeder;

class AssignPermissionToRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Just add permissions with role you want to assign like the comment below
        $permissions = [
//            [
//                'name' => 'createTicket',
//                'display_name' => 'ایجاد تیکت',
//                'description' => 'دسترسی به روت دیتای لازم برای ایجاد تیکت',
//                'role_id' => 1,
//            ],
//            [
//                'name' => 'storeTicket',
//                'display_name' => 'ذخیره تیکت',
//                'description' => 'اجازه اضافه کردن یک تیکت جدید',
//                'role_id' => 1,
//            ]
        ];
        if (empty($permissions)) {
            return;
        }
        foreach ($permissions as $permission) {
            $roleId = $permission['role_id'];
            unset($permission['role_id']);
            $permissionInstance = Permission::updateOrCreate(['name' => $permission['name']], $permission);
            $permissionInstance->roles()->syncWithoutDetaching([$roleId]);
        }
    }
}
