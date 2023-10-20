<?php

namespace Database\Seeders\Other;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now('Asia/Tehran');
        $table = 'tags';
        DB::table($table)->delete();
        $data = [
            [
                'id' => '1', 'name' => 'نظام جدید', 'value' => 'نظام_آموزشی_جدید', 'tag_group_id' => '1',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '2', 'name' => 'نظام قدیم', 'value' => 'نظام_آموزشی_قدیم', 'tag_group_id' => '1',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '3', 'name' => 'هفتم', 'value' => 'هفتم', 'tag_group_id' => '2', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '4', 'name' => 'هشتم', 'value' => 'هشتم', 'tag_group_id' => '2', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '5', 'name' => 'نهم', 'value' => 'نهم', 'tag_group_id' => '2', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '6', 'name' => 'کنکور', 'value' => 'کنکور', 'tag_group_id' => '2', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '7', 'name' => 'دهم', 'value' => 'دهم', 'tag_group_id' => '2', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '8', 'name' => 'یازدهم', 'value' => 'یازدهم', 'tag_group_id' => '2', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '9', 'name' => 'دوازدهم', 'value' => 'دوازدهم', 'tag_group_id' => '2', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '10', 'name' => 'المپیاد', 'value' => 'المپیاد', 'tag_group_id' => '2', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '11', 'name' => 'اول دبیرستان', 'value' => 'اول_دبیرستان', 'tag_group_id' => '2',
                'enable' => '0', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '12', 'name' => 'دوم دبیرستان', 'value' => 'دوم_دبیرستان', 'tag_group_id' => '2',
                'enable' => '0', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '13', 'name' => 'سوم دبیرستان', 'value' => 'سوم_دبیرستان', 'tag_group_id' => '2',
                'enable' => '0', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '14', 'name' => 'چهارم دبیرستان', 'value' => 'چهارم_دبیرستان', 'tag_group_id' => '2',
                'enable' => '0', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '15', 'name' => 'رشته ریاضی', 'value' => 'رشته_ریاضی', 'tag_group_id' => '3', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '16', 'name' => 'رشته تجربی', 'value' => 'رشته_تجربی', 'tag_group_id' => '3', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '17', 'name' => 'رشته انسانی', 'value' => 'رشته_انسانی', 'tag_group_id' => '3', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '18', 'name' => 'متوسطه اول', 'value' => 'متوسطه1', 'tag_group_id' => '3', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '105', 'name' => 'اخلاق', 'value' => 'اخلاق', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '106', 'name' => 'دین و زندگی', 'value' => 'دین_و_زندگی', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '107', 'name' => 'ریاضی انسانی', 'value' => 'ریاضی_انسانی', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '108', 'name' => 'ریاضی و آمار', 'value' => 'ریاضی_و_آمار', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '109', 'name' => 'زبان انگلیسی', 'value' => 'زبان_انگلیسی', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '110', 'name' => 'مشاوره', 'value' => 'مشاوره', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '111', 'name' => 'منطق', 'value' => 'منطق', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '112', 'name' => 'هندسه', 'value' => 'هندسه', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '113', 'name' => 'فیزیک', 'value' => 'فیزیک', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '114', 'name' => 'عربی', 'value' => 'عربی', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '115', 'name' => 'شیمی', 'value' => 'شیمی', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '116', 'name' => 'زیست شناسی', 'value' => 'زیست_شناسی', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '117', 'name' => 'زبان و ادبیات فارسی', 'value' => 'زبان_و_ادبیات_فارسی', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '118', 'name' => 'ریاضی پایه', 'value' => 'ریاضی_پایه', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '119', 'name' => 'ریاضی تجربی', 'value' => 'ریاضی_تجربی', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '120', 'name' => 'المپیاد نجوم', 'value' => 'المپیاد_نجوم', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '121', 'name' => 'المپیاد فیزیک', 'value' => 'المپیاد_فیزیک', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '122', 'name' => 'گسسته', 'value' => 'گسسته', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '123', 'name' => 'هندسه کنکور', 'value' => 'هندسه_کنکور', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '124', 'name' => 'هندسه پایه', 'value' => 'هندسه_پایه', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '125', 'name' => 'دیفرانسیل', 'value' => 'دیفرانسیل', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '126', 'name' => 'حسابان', 'value' => 'حسابان', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '127', 'name' => 'جبر و احتمال', 'value' => 'جبر_و_احتمال', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '128', 'name' => 'تحلیلی', 'value' => 'تحلیلی', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '129', 'name' => 'آمار و احتمال', 'value' => 'آمار_و_احتمال', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '130', 'name' => 'ریاضی', 'value' => 'ریاضی', 'tag_group_id' => '4', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '131', 'name' => 'آمار و مدلسازی', 'value' => 'آمار_و_مدلسازی', 'tag_group_id' => '4',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '132', 'name' => 'محمد علی امینی راد', 'value' => 'محمد_علی_امینی_راد', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '133', 'name' => 'یاشار بهمند', 'value' => 'یاشار_بهمند', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '134', 'name' => 'مصطفی جعفری نژاد', 'value' => 'مصطفی_جعفری_نژاد', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '135', 'name' => 'سید حسام الدین جلالی', 'value' => 'سید_حسام_الدین_جلالی',
                'tag_group_id' => '5', 'enable' => '1', 'description' => null, 'created_at' => $now,
                'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '136', 'name' => 'رضا آقاجانی', 'value' => 'رضا_آقاجانی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '137', 'name' => 'مهدی امینی راد', 'value' => 'مهدی_امینی_راد', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '138', 'name' => 'محمد پازوکی', 'value' => 'محمد_پازوکی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '139', 'name' => 'جلال موقاری', 'value' => 'جلال_موقاری', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '140', 'name' => 'پوریا رحیمی', 'value' => 'پوریا_رحیمی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '141', 'name' => 'عباس راستی بروجنی', 'value' => 'عباس_راستی_بروجنی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '142', 'name' => 'مسعود حدادی', 'value' => 'مسعود_حدادی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '143', 'name' => 'ابوالفضل جعفری', 'value' => 'ابوالفضل_جعفری', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '144', 'name' => 'ارشی', 'value' => 'ارشی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '145', 'name' => 'وحید کبریایی', 'value' => 'وحید_کبریایی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '146', 'name' => 'رضا شامیزاده', 'value' => 'رضا_شامیزاده', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '147', 'name' => 'کاظم کاظمی', 'value' => 'کاظم_کاظمی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '148', 'name' => 'عبدالرضا مرادی', 'value' => 'عبدالرضا_مرادی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '149', 'name' => 'محمد صادقی', 'value' => 'محمد_صادقی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '150', 'name' => 'هامون سبطی', 'value' => 'هامون_سبطی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '151', 'name' => 'داریوش راوش', 'value' => 'داریوش_راوش', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '152', 'name' => 'میثم  حسین خانی', 'value' => 'میثم__حسین_خانی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '153', 'name' => 'جعفر رنجبرزاده', 'value' => 'جعفر_رنجبرزاده', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '154', 'name' => 'مهدی تفتی', 'value' => 'مهدی_تفتی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '155', 'name' => 'کیاوش فراهانی', 'value' => 'کیاوش_فراهانی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '156', 'name' => 'علی اکبر عزتی', 'value' => 'علی_اکبر_عزتی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '157', 'name' => 'درویش', 'value' => 'درویش', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '158', 'name' => 'کازرانیان', 'value' => 'کازرانیان', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '159', 'name' => 'نادریان', 'value' => 'نادریان', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '160', 'name' => 'حمید فدایی فرد', 'value' => 'حمید_فدایی_فرد', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '161', 'name' => 'پیمان طلوعی', 'value' => 'پیمان_طلوعی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '162', 'name' => 'علیرضا رمضانی', 'value' => 'علیرضا_رمضانی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '163', 'name' => 'فرشید داداشی', 'value' => 'فرشید_داداشی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '164', 'name' => 'جهانبخش', 'value' => 'جهانبخش', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '165', 'name' => 'حامد پویان نظر', 'value' => 'حامد_پویان_نظر', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '166', 'name' => 'محسن معینی', 'value' => 'محسن_معینی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '167', 'name' => 'مهدی صنیعی طهرانی', 'value' => 'مهدی_صنیعی_طهرانی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '168', 'name' => 'محمد حسین شکیباییان', 'value' => 'محمد_حسین_شکیباییان', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '169', 'name' => 'روح الله حاجی سلیمانی', 'value' => 'روح_الله_حاجی_سلیمانی',
                'tag_group_id' => '5', 'enable' => '1', 'description' => null, 'created_at' => $now,
                'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '170', 'name' => 'جعفری', 'value' => 'جعفری', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '171', 'name' => 'محمد حسین انوشه', 'value' => 'محمد_حسین_انوشه', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '172', 'name' => 'محمد رضا آقاجانی', 'value' => 'محمد_رضا_آقاجانی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '173', 'name' => 'مهدی ناصر شریعت', 'value' => 'مهدی_ناصر_شریعت', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '174', 'name' => 'میلاد ناصح زاده', 'value' => 'میلاد_ناصح_زاده', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '175', 'name' => 'پدرام علیمرادی', 'value' => 'پدرام_علیمرادی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '176', 'name' => 'ناصر حشمتی', 'value' => 'ناصر_حشمتی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '177', 'name' => 'مهدی جلادتی', 'value' => 'مهدی_جلادتی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '178', 'name' => 'عمار تاج بخش', 'value' => 'عمار_تاج_بخش', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '179', 'name' => 'محسن آهویی', 'value' => 'محسن_آهویی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '180', 'name' => 'خسرو محمد زاده', 'value' => 'خسرو_محمد_زاده', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '181', 'name' => 'محمدامین نباخته', 'value' => 'محمدامین_نباخته', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '182', 'name' => 'علی صدری', 'value' => 'علی_صدری', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '183', 'name' => 'محمد رضا حسینی فرد', 'value' => 'محمد_رضا_حسینی_فرد', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '184', 'name' => 'محمد صادق ثابتی', 'value' => 'محمد_صادق_ثابتی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '185', 'name' => 'جواد نایب کبیر', 'value' => 'جواد_نایب_کبیر', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '186', 'name' => 'محمدرضا مقصودی', 'value' => 'محمدرضا_مقصودی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '187', 'name' => 'محسن شهریان', 'value' => 'محسن_شهریان', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '188', 'name' => 'حسین کرد', 'value' => 'حسین_کرد', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '189', 'name' => 'شهروز رحیمی', 'value' => 'شهروز_رحیمی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '190', 'name' => 'حسن مرصعی', 'value' => 'حسن_مرصعی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '191', 'name' => 'سروش معینی', 'value' => 'سروش_معینی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '192', 'name' => 'شاه محمدی', 'value' => 'شاه_محمدی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '193', 'name' => 'بهمن مؤذنی پور', 'value' => 'بهمن_مؤذنی_پور', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '194', 'name' => 'سیروس نصیری', 'value' => 'سیروس_نصیری', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '195', 'name' => 'محمد رضا یاری', 'value' => 'محمد_رضا_یاری', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '196', 'name' => 'احسان گودرزی', 'value' => 'احسان_گودرزی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '197', 'name' => 'میثم حسین خانی', 'value' => 'میثم_حسین_خانی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '198', 'name' => 'محمد رضا لکستانی', 'value' => 'محمد_رضا_لکستانی', 'tag_group_id' => '5',
                'enable' => '1', 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
            [
                'id' => '199', 'name' => 'امید زاهدی', 'value' => 'امید_زاهدی', 'tag_group_id' => '5', 'enable' => '1',
                'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null
            ],
        ];
        DB::table($table)
            ->insert($data); // Query Builder
    }
}
