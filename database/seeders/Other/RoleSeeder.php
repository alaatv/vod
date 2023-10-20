<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
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
                'isDefault'    => '0',
                'name'         => 'admin',
                'display_name' => 'مدیر کل',
                'description'  => 'اکانت مدیریتی اصلی سایت',
                'created_at'   => null,
                'updated_at'   => null,
            ],
            [
                'id'           => '2',
                'isDefault'    => '0',
                'name'         => 'operator',
                'display_name' => 'مسئول اجرایی',
                'description'  => 'قراره لیست سفارشات رو بتونه ببینه',
                'created_at'   => '2017-02-03 18:23:41',
                'updated_at'   => '2017-02-03 18:23:41',
            ],
            [
                'id'           => '3',
                'isDefault'    => '0',
                'name'         => 'consultant',
                'display_name' => 'مشاور',
                'description'  => 'مشاوران تحصیلی',
                'created_at'   => '2017-02-04 11:56:19',
                'updated_at'   => '2017-02-04 11:56:19',
            ],
            [
                'id'           => '4',
                'isDefault'    => '0',
                'name'         => 'support',
                'display_name' => 'پشتیبان',
                'description'  => '',
                'created_at'   => '2017-06-11 11:02:50',
                'updated_at'   => '2017-06-11 11:02:50',
            ],
            [
                'id'           => '5',
                'isDefault'    => '0',
                'name'         => 'education',
                'display_name' => 'آموزش',
                'description'  => 'محصول',
                'created_at'   => '2017-10-12 13:39:18',
                'updated_at'   => '2017-11-18 07:48:54',
            ],
            [
                'id'           => '6',
                'isDefault'    => '0',
                'name'         => 'employee',
                'display_name' => 'کارمند',
                'description'  => null,
                'created_at'   => '2017-11-11 12:49:42',
                'updated_at'   => '2018-09-24 17:28:54',
            ],
            [
                'id'           => '7',
                'isDefault'    => '0',
                'name'         => 'bookPostMan',
                'display_name' => 'ارسال کننده کتاب',
                'description'  => 'این نقش برای ارسال کننده کتاب های ادبیات هامون سبطی و زیست راستی ایجاد شد',
                'created_at'   => '2017-12-05 07:39:56',
                'updated_at'   => '2017-12-05 07:39:56',
            ],
            [
                'id'           => '8',
                'isDefault'    => '0',
                'name'         => 'onlineNoroozMarketing',
                'display_name' => 'فروش اردو غیر حضوری 97',
                'description'  => 'فروش اردوی غیر حضوری نوروز 97',
                'created_at'   => '2018-02-12 08:29:48',
                'updated_at'   => '2018-02-12 08:29:48',
            ],
            [
                'id'           => '9',
                'isDefault'    => '0',
                'name'         => 'sharifSchoolRegister',
                'display_name' => 'ثبت نام دبیرستان شریف',
                'description'  => 'پیگیری آمار ثبت نام دبیرستان شریف',
                'created_at'   => '2018-05-11 10:58:12',
                'updated_at'   => '2018-05-11 10:58:12',
            ],
            [
                'id'           => '10',
                'isDefault'    => '0',
                'name'         => 'teacher',
                'display_name' => 'دبیر',
                'description'  => 'دبیر',
                'created_at'   => '2018-08-15 11:01:04',
                'updated_at'   => '2018-08-15 11:01:04',
            ],
            [
                'id'           => '11',
                'isDefault'    => '0',
                'name'         => 'user management',
                'display_name' => 'مدیریت کاربران',
                'description'  => null,
                'created_at'   => '2018-10-21 12:52:45',
                'updated_at'   => '2018-10-21 12:52:45',
            ],
        ];
        DB::table('roles')
          ->insert($data); // Query Builder
    }
}
