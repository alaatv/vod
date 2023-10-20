<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class SetPermissionAndRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:permission:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set permissions and roles';
    protected $permissions;
    protected $roles;
    protected $startId;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        //start id is last id in DB; if last record is for example 10, you should write 10 below
        $this->startId = 338;
        $this->permissions = [
            [
                'id' => ++$this->startId,
                'name' => 'examStore',
                'display_name' => 'ساخت یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examUpdate',
                'display_name' => 'ویرایش یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examIndex',
                'display_name' => 'نمایش لیست آزمونها',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examDestroy',
                'display_name' => 'حذف یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examConfirm',
                'display_name' => 'تایید یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examConfig',
                'display_name' => 'تنظیم کانفیگ برای یک آزمون ( بیشتر کاربرد برای تولید کارنامه)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examShow',
                'display_name' => 'نمایش یک ازمون به صورت تکی',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examAttachCategory',
                'display_name' => 'چسباندن دسته بندی به آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examDetachCategory',
                'display_name' => 'برداشتن دسته بندی آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'searchActivitylog',
                'display_name' => 'نمایش لاگهای سوالات و ازمونها',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'commentActivitylog',
                'display_name' => 'کامنت نویسی برای لاگها',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'categoryCreate',
                'display_name' => 'ساخت دفترچه',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'categoryUpdate',
                'display_name' => 'ویرایش دفترچه',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'categoryIndex',
                'display_name' => 'نمایش لیست دفترچه ها',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'categoryShow',
                'display_name' => 'نمایش یک دفترچه',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionAttachV2',
                'display_name' => 'الصاق سوال به آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionDetach',
                'display_name' => 'حذف یک سوال از آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionAttachSubcategory',
                'display_name' => 'افزودن ویدیو به آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionAttach',
                'display_name' => 'ساخت یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionBooklet',
                'display_name' => 'نمایش دفترچه سوال و جواب با فرمت pdf یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionBookletUpload',
                'display_name' => 'آپلود دفترچه سوال و جواب با فرمت pdf به یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionFile',
                'display_name' => 'ساخت فایل json  سوالات و جوابها برای یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionVideos',
                'display_name' => 'نمایش ویدیوهای یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionZirgoroohShow',
                'display_name' => 'نمایش زیرگروه های یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionZirgorooh',
                'display_name' => 'افزودن زیرگروه به یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionZirgoroohDelete',
                'display_name' => 'حذف یک زیرگروه از یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionAttachShow',
                'display_name' => 'نمایش لیست سوالات یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionShowcategorires',
                'display_name' => 'نمایش لیست دروس یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionZirgoroohCopyzirgorooh',
                'display_name' => 'کپی زیر گروه های یک ازمون برای یک آزمون دیگر',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examquestionShortlink',
                'display_name' => 'ساخت کوتاه کننده لینک برای یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examreportIndexParticipants',
                'display_name' => 'نمایش کانامه شرکت کنندگان یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'examreportIndexLessons',
                'display_name' => 'نمایش درصد و نمرات شرکت کنندگان یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'optionIndex',
                'display_name' => 'نمایش سال،  مرجع و رشته برای تنظیم آن برای یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'optionStore',
                'display_name' => 'ساخت سال ، مرجع و رشته برای تنظیم آن برای یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'optionShow',
                'display_name' => 'نمایش سال،  مرجع و رشته برای تنظیم آن برای یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'optionUpdate',
                'display_name' => 'ویرایش سال،  مرجع و رشته برای تنظیم آن برای یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'optionDelete',
                'display_name' => 'حذف سال،  مرجع و رشته برای تنظیم آن برای یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionStore',
                'display_name' => 'ایجاد یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionUpdate',
                'display_name' => 'ویرایش یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionUpload',
                'display_name' => 'آپلود تصویر برای قرار دادن در متن سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionDestroy',
                'display_name' => 'حذف یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionIndex',
                'display_name' => 'نمایش لیست سوالات',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionConfirm',
                'display_name' => 'تایید یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionUnconfirm',
                'display_name' => 'تایید نکردن یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionStatus',
                'display_name' => 'ثبت وضعیت برای یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionStatuses',
                'display_name' => 'نمایش لیست وضعیت هایی که می توان برای یک سوال ثبت کرد',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionShow',
                'display_name' => 'نمایش یک سوال به صورت تکی',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionAttachStatementphoto',
                'display_name' => 'آپلود تصویر برای متن سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionAttachAnswerphoto',
                'display_name' => 'آپلود تصویر برای جواب سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionDetachStatementphoto',
                'display_name' => 'برداشتن عکس از متن یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionDetachAnswerphoto',
                'display_name' => 'برداشتن عکس از جواب یک سوال',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'questionExport',
                'display_name' => 'خروجی گرفتن از سوالات یک آزمون',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'subcategoryStore',
                'display_name' => 'افزودن زیر دسته بندی',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'subcategoryUpdate',
                'display_name' => 'ویرایش زیر دسته بندی',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'subcategoryShow',
                'display_name' => 'نمایش یک زیر دسته بندی',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => ++$this->startId,
                'name' => 'subcategoryIndex',
                'display_name' => 'نمایش لیست زیر دسته بندی ها',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->roles = [
            [
                'isDefault' => 0,
                'name' => 'typist',
                'display_name' => 'تایپیست',
                'permissions' => [
                    'searchActivitylog', 'commentActivitylog', 'examquestionAttach', 'questionStore', 'optionIndex',
                    'optionUpdate', 'optionDelete', 'questionUpdate', 'questionUpload',
                    'questionIndex', 'questionStatus', 'questionStatuses', 'questionShow', 'subcategoryShow',
                    'subcategoryIndex', 'categoryShow', 'categoryIndex'
                ]
            ],
            [
                'isDefault' => 0,
                'name' => 'designer',
                'display_name' => 'طراح',
                'permissions' => [
                    'searchActivitylog', 'commentActivitylog', 'examquestionAttach', 'questionStore', 'optionIndex',
                    'optionUpdate', 'optionDelete', 'questionUpdate', 'questionUpload',
                    'questionIndex', 'questionStatus', 'questionStatuses', 'questionShow',
                    'questionAttachStatementphoto', 'questionAttachAnswerphoto',
                    'subcategoryShow', 'subcategoryIndex', 'categoryShow', 'categoryIndex'
                ]
            ],
            [
                'isDefault' => 0,
                'name' => 'sampleReader',
                'display_name' => 'نمونه خوان',
                'permissions' => [
                    'searchActivitylog', 'commentActivitylog', 'optionIndex', 'optionUpdate', 'optionDelete',
                    'questionUpdate',
                    'questionIndex', 'questionStatus', 'questionStatuses', 'questionShow'
                ]
            ],
            [
                'isDefault' => 0,
                'name' => 'editor',
                'display_name' => 'ویراستار',
                'permissions' => [
                    'searchActivitylog', 'commentActivitylog', 'optionIndex', 'optionUpdate', 'optionDelete',
                    'questionUpdate',
                    'questionIndex', 'questionStatus', 'questionStatuses', 'questionShow'
                ]
            ],
            [
                'isDefault' => 0,
                'name' => 'teamLeader',
                'display_name' => 'سرگروه',
                'permissions' => [
                    'searchActivitylog', 'commentActivitylog', 'optionIndex', 'optionUpdate', 'optionDelete',
                    'questionUpdate',
                    'questionIndex', 'questionStatus', 'questionStatuses', 'questionShow'
                ]
            ],
            [
                'isDefault' => 0,
                'name' => 'admin3a',
                'display_name' => 'ادمین',
                'permissions' => [
                    'examStore', 'examUpdate', 'examIndex', 'examDestroy', 'examConfirm', 'examConfig', 'examShow',
                    'searchActivitylog',
                    'commentActivitylog', 'categoryCreate', 'categoryUpdate', 'categoryIndex', 'categoryShow',
                    'examquestionAttachV2',
                    'examquestionDetach', 'examquestionAttachSubcategory', 'examquestionAttach', 'examquestionBooklet',
                    'examquestionBookletUpload', 'examquestionFile', 'examquestionVideos', 'examquestionZirgoroohShow',
                    'examquestionZirgorooh', 'examquestionZirgoroohDelete', 'examquestionAttachShow',
                    'examquestionShowcategorires',
                    'examquestionZirgoroohCopyzirgorooh', 'examquestionShortlink', 'examreportIndexParticipants',
                    'examreportIndexLessons', 'optionIndex', 'optionStore', 'optionShow', 'optionUpdate',
                    'optionDelete',
                    'questionUpdate', 'questionUpload', 'questionDestroy', 'questionIndex', 'questionConfirm',
                    'questionUnconfirm',
                    'questionStatus', 'questionStatuses', 'questionShow', 'questionAttachStatementphoto',
                    'questionAttachAnswerphoto',
                    'questionDetachStatementphoto', 'questionDetachAnswerphoto', 'questionExport', 'examAttachCategory',
                    'examDetachCategory', 'questionStore', 'subcategoryShow', 'subcategoryIndex', 'subcategoryStore',
                    'subcategoryUpdate'
                ]
            ]
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        permission::insert($this->permissions);
        foreach ($this->roles as $role) {
            $permissionIds = Permission::whereIn('name', $role['permissions'])->get(['id'])->pluck('id')->toArray();
            $createdRole = Role::create($role);
            $createdRole->permissions()->sync($permissionIds);
        }
        $this->info('done');
        return 0;
    }
}
