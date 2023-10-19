<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeaRolePermissionSeeder extends Seeder
{

    public function run()
    {
        $this->appendPermissions();
        $this->appendRoles();
    }

    private function appendRoles()
    {
        $roles = [
           [
                'isDefault'    => '0',
                'name'         => 'historyHead',
                'display_name' => 'سرگروه تاریخ',
                'description'  => 'سرگروه تاریخ',
                'created_at'   => now(),
                'updated_at'   => null,
            ],
            [
                'isDefault'    => '0',
                'name'         => 'geographyHead',
                'display_name' => 'سرگروه جغرافیا',
                'description'  => 'سرگروه جغرافیا',
                'created_at'   => now(),
                'updated_at'   => null,
            ],
            [
                'isDefault'    => '0',
                'name'         => 'socialSciencesHead',
                'display_name' => 'سرگروه علوم اجتماعی',
                'description'  => 'سرگروه علوم اجتماعی',
                'created_at'   => now(),
                'updated_at'   => null,
            ],
            [
                'isDefault'    => '0',
                'name'         => 'PhilosophyHead',
                'display_name' => 'سرگروه فلسفه',
                'description'  => 'سرگروه فلسفه',
                'created_at'   => now(),
                'updated_at'   => null,
            ],
            [
                'isDefault'    => '0',
                'name'         => 'psychologyHead',
                'display_name' => 'سرگروه روانشناسی',
                'description'  => 'سرگروه روانشناسی',
                'created_at'   => now(),
                'updated_at'   => null,
            ],
            [
                'isDefault'    => '0',
                'name'         => 'statisticsAndProbabilityHead',
                'display_name' => 'سرگروه آمار و احتمالات',
                'description'  => 'سرگروه آمار و احتمالات',
                'created_at'   => now(),
                'updated_at'   => null,
            ],

        ];
        DB::table('roles')->insert($roles);
    }

    private function appendPermissions()
    {
        $permissions = [
            [
                'name'         => 'exam_store',
                'display_name' => 'ذخیره آزمون',
                'description'  => 'ذخیره آزمون',
            ],
            [
                'name'         => 'exam_update',
                'display_name' => 'بروز رسانی آزمون',
                'description'  => 'بروز رسانی آزمون',
            ],
            [
                'name'         => 'exam_index',
                'display_name' => 'مشاهده لیست آزمون',
                'description'  => 'مشاهده لیست آزمون',
            ],
            [
                'name'         => 'exam_destroy',
                'display_name' => 'حذف آزمون',
                'description'  => 'حذف آزمون',
            ],
            [
                'name'         => 'exam_confirm',
                'display_name' => 'تایید آزمون',
                'description'  => 'تایید آزمون',
            ],
            [
                'name'         => 'exam_config',
                'display_name' => 'کانفیگ آزمون',
                'description'  => 'کانفیگ آزمون',
            ],
            [
                'name'         => 'activitylog_search',
                'display_name' => 'جسجتجو در لاگ فعالیت ها',
                'description'  => 'جسجتجو در لاگ فعالیت ها',
            ],
            [
                'name'         => 'category_create',
                'display_name' => 'ایچاد دسته بندی',
                'description'  => 'ایچاد دسته بندی',
            ],
            [
                'name'         => 'category_update',
                'display_name' => 'بروزرسانی دسته بندی',
                'description'  => 'بروزرسانی دسته بندی',
            ],
            [
                'name'         => 'category_index',
                'display_name' => 'مشاهده لیست دسته بندی',
                'description'  => 'مشاهده لیست دسته بندی',
            ],
            [
                'name'         => 'category_show',
                'display_name' => 'مشاهده دسته بندی',
                'description'  => 'مشاهده دسته بندی',
            ],
            [
                'name'         => 'examquestion_attach.v2',
                'display_name' => '(ورژن ۲)تخصیص سوال به آزمون',
                'description'  => 'تخصیص سوال به آزمون(ورژن ۲)',
            ],
            [
                'name'         => 'examquestion_attach',
                'display_name' => 'تخصیص سوال به آزمون',
                'description'  => 'تخصیص سوال به آزمون',
            ],
            [
                'name'         => 'examquestion_attach_subcategory',
                'display_name' => 'افزودن سوال به آزمون زیرگروه',
                'description'  => 'افزودن سوال به آزمون زیرگروه',
            ],
            [
                'name'         => 'examquestion_import',
                'display_name' => 'افزودن سوال به آزمون',
                'description'  => 'افزودن سوال به آزمون',
            ],
            [
                'name'         => 'examquestion_booklet',
                'display_name' => 'جزوه سوالات آزمون',
                'description'  => 'جزوه سوالات آزمون',
            ],
            [
                'name'         => 'examquestion_booklet_upload',
                'display_name' => 'آپلود جزوه سوالات آزمون',
                'description'  => 'آپلود جزوه سوالات آزمون',
            ],
            [
                'name'         => 'examquestion_file',
                'display_name' => 'فایل سوالات آزمون',
                'description'  => 'فایل سوالات آزمون',
            ],
            [
                'name'         => 'examquestion_videos',
                'display_name' => 'ویدئوهای سوالات آزمون',
                'description'  => 'ویدئوهای سوالات آزمون',
            ],
            [
                'name'         => 'examquestion_zirgorooh_show',
                'display_name' => 'نمایش سوالات آزمون یک زیرگروه',
                'description'  => 'نمایش سوالات آزمون یک زیرگروه',
            ],
            [
                'name'         => 'examquestion_zirgorooh',
                'display_name' => 'سوالات آزمون زیرگروه',
                'description'  => 'سوالات آزمون زیرگروه',
            ],
            [
                'name'         => 'examquestion_zirgorooh_delete',
                'display_name' => 'حذف سوالات آزمون زیرگروه',
                'description'  => 'حذف سوالات آزمون زیرگروه',
            ],
            [
                'name'         => 'examquestion_attach_show',
                'display_name' => 'نمایش تخصیص سوالات آزمون',
                'description'  => 'نمایش تخصیص سوالات آزمون',
            ],
            [
                'name'         => 'examquestion_showcategorires',
                'display_name' => 'نمایش گروه بندی سوالات آزمون',
                'description'  => 'نمایش گروه بندی سوالات آزمون',
            ],
            [
                'name'         => 'examreport_index_participants',
                'display_name' => 'گزارش شرکت کنندگان در آزمون',
                'description'  => 'گزارش شرکت کنندگان در آزمون',
            ],
            [
                'name'         => 'examreport_index_lessons',
                'display_name' => 'گزارش دروس آزمون',
                'description'  => 'گزارش دروس آزمون',
            ],
            [
                'name'         => 'option_index',
                'display_name' => 'لیست آپشن',
                'description'  => 'لیست آپشن',
            ],
            [
                'name'         => 'question_store',
                'display_name' => 'ذخیره سوال',
                'description'  => 'ذخیره سوال',
            ],
            [
                'name'         => 'question_update',
                'display_name' => 'بروز رسانی سوال',
                'description'  => 'بروز رسانی سوال',
            ],
            [
                'name'         => 'question_upload',
                'display_name' => 'آپلود سوال',
                'description'  => 'آپلود سوال',
            ],
            [
                'name'         => 'question_destro',
                'display_name' => 'حذف سوال',
                'description'  => 'حذف سوال',
            ],
            [
                'name'         => 'question_index',
                'display_name' => 'فهرست سوالات',
                'description'  => 'فهرست سوالات',
            ],
            [
                'name'         => 'question_confirm',
                'display_name' => 'تایید سوال',
                'description'  => 'تایید سوال',
            ],
            [
                'name'         => 'question_status',
                'display_name' => 'وضعیت سوال',
                'description'  => 'وضعیت سوال',
            ],
            [
                'name'         => 'question_statuses',
                'display_name' => 'وضعیت های سوال',
                'description'  => 'وضعیت های سوال',
            ],
            [
                'name'         => 'subcategory_store',
                'display_name' => 'ذخیره زیر دسته بندی',
                'description'  => 'ذخیره زیر دسته بندی',
            ],
            [
                'name'         => 'subcategory_update',
                'display_name' => 'بروزرسانی زیر دسته بندی',
                'description'  => 'بروزرسانی زیر دسته بندی',
            ],
            [
                'name'         => 'subcategory_show',
                'display_name' => 'نمایش زیر دسته بندی',
                'description'  => 'نمایش زیر دسته بندی',
            ],
            [
                'name'         => 'subcategory_index',
                'display_name' => 'فهرست زیر دسته بندی ها',
                'description'  => 'فهرست زیر دسته بندی ها',
            ],
            [
                'name'         => 'user_update',
                'display_name' => 'بروزرسانی کاربر',
                'description'  => 'بروزرسانی کاربر',
            ],
            [
                'name'         => 'user_delete',
                'display_name' => 'حذف کاربر',
                'description'  => 'حذف کاربر',
            ],
            [
                'name'         => 'exam_index_lessons',
                'display_name' => 'لیست دروس آزمون',
                'description'  => 'لیست دروس آزمون',
            ],
        ];
        DB::table('permissions')->insert($permissions);
    }
}
