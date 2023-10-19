<?php

namespace App\Console\Commands;

use App\Console\Commands\CategoryTree\Ensani;
use App\Console\Commands\CategoryTree\Riazi;
use App\Console\Commands\CategoryTree\Tajrobi;
use App\Models\Category;
use Illuminate\Console\Command;

class InitializeCategoryTreeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:seed:init:categorise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'seeders category table';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('get Start - Category Tree');
        $data = $this->makeAlaaArray();
        Category::truncate();
        Category::create($data);
        $this->info('Finish!');
    }

    /**
     * @return array
     */
    private function makeAlaaArray(): array
    {
        $omoomi = [
            [
                'name' => 'دین و زندگی',
                'tags' => ['دین_و_زندگی'],
                'children' => [],
            ],
            [
                'name' => 'زبان و ادبیات فارسی',
                'tags' => ['زبان_و_ادبیات_فارسی'],
                'children' => [],
            ],
            [
                'name' => 'عربی',
                'tags' => ['عربی'],
                'children' => [],
            ],
            [
                'name' => 'زبان انگلیسی',
                'tags' => ['زبان_انگلیسی'],
                'children' => [],
            ],
            [
                'name' => 'مشاوره',
                'tags' => ['مشاوره'],
                'children' => [],
            ],
        ];

        $dahomR = $omoomi + [
                [
                    'name' => 'ریاضی پایه',
                    'tags' => ['ریاضی_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'هندسه پایه',
                    'tags' => ['هندسه_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'فیزیک',
                    'tags' => ['فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'شیمی',
                    'tags' => ['شیمی'],
                    'children' => [],
                ],
                [
                    'name' => 'نگارش',
                    'tags' => ['نگارش'],
                    'enable' => false,
                    'children' => [],
                ],
            ];
        $dahomT = $omoomi + [
                [
                    'name' => 'ریاضی 1',
                    'tags' => [
                        'ریاضی_پایه',
                        'ریاضی',
                        'نظام_آموزشی_جدید',
                        'ریاضی1',
                    ],
                    'children' => [],
                ],
                [
                    'name' => 'زیست شناسی',
                    'tags' => ['زیست_شناسی'],
                    'children' => [],
                ],
                [
                    'name' => 'فیزیک',
                    'tags' => ['فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'شیمی',
                    'tags' => ['شیمی'],
                    'children' => [],
                ],
                [
                    'name' => 'نگارش',
                    'tags' => ['نگارش'],
                    'enable' => false,
                    'children' => [],
                ],
            ];
        $dahomE = $omoomi + [
                [
                    'name' => 'اقتصاد',
                    'tags' => ['اقتصاد'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'تاریخ',
                    'tags' => ['تاریخ'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'جامعه شناسی',
                    'tags' => ['جامعه_شناسی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'جغرافیای ایران',
                    'tags' => ['جغرافیای_ایران'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'ریاضی و آمار',
                    'tags' => ['ریاضی_و_آمار'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'علوم و فنون ادبی',
                    'tags' => ['علوم_و_فنون_ادبی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'منطق',
                    'tags' => ['منطق'],
                    'children' => [],
                ],
            ];

        $yazdahomR = $omoomi + [
                [
                    'name' => 'حسابان',
                    'tags' => ['حسابان'],
                    'children' => [],
                ],
                [
                    'name' => 'آمار و احتمال',
                    'tags' => ['آمار_و_احتمال'],
                    'children' => [],
                ],
                [
                    'name' => 'هندسه پایه',
                    'tags' => ['هندسه_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'فیزیک',
                    'tags' => ['فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'شیمی',
                    'tags' => ['شیمی'],
                    'children' => [],
                ],
                [
                    'name' => 'زمین شناسی',
                    'tags' => ['زمین_شناسی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'نگارش',
                    'tags' => ['نگارش'],
                    'enable' => false,
                    'children' => [],
                ],
            ];
        $yazdahomT = $omoomi + [
                [
                    'name' => 'ریاضی پایه',
                    'tags' => ['ریاضی_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'زیست شناسی',
                    'tags' => ['زیست_شناسی'],
                    'children' => [],
                ],
                [
                    'name' => 'فیزیک',
                    'tags' => ['فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'شیمی',
                    'tags' => ['شیمی'],
                    'children' => [],
                ],
                [
                    'name' => 'زمین شناسی',
                    'tags' => ['زمین_شناسی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'نگارش',
                    'tags' => ['نگارش'],
                    'enable' => false,
                    'children' => [],
                ],
            ];
        $yazdahomE = $omoomi + [
                [
                    'name' => 'تاریخ',
                    'tags' => ['تاریخ'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'جامعه شناسی',
                    'tags' => ['جامعه_شناسی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'جغرافیا',
                    'tags' => ['جغرافیا'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'روان شناسی',
                    'tags' => ['روان_شناسی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'ریاضی و آمار',
                    'tags' => ['ریاضی_و_آمار'],
                    'children' => [],
                ],
                [
                    'name' => 'علوم و فنون ادبی',
                    'tags' => ['علوم_و_فنون_ادبی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'فلسفه',
                    'tags' => ['فلسفه'],
                    'children' => [],
                ],
                [
                    'name' => 'نگارش',
                    'tags' => ['نگارش'],
                    'enable' => false,
                    'children' => [],
                ],
            ];

        $davazdahomR = $omoomi + [
                [
                    'name' => 'حسابان',
                    'tags' => ['حسابان'],
                    'children' => [],
                ],
                [
                    'name' => 'گسسته',
                    'tags' => ['گسسته'],
                    'children' => [],
                ],
                [
                    'name' => 'هندسه پایه',
                    'tags' => ['هندسه_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'فیزیک',
                    'tags' => ['فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'شیمی',
                    'tags' => ['شیمی'],
                    'children' => [],
                ],
                [
                    'name' => 'نگارش',
                    'tags' => ['نگارش'],
                    'enable' => false,
                    'children' => [],
                ],
            ];
        $davazdahomT = $omoomi + [
                [
                    'name' => 'ریاضی پایه',
                    'tags' => ['ریاضی_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'زیست شناسی',
                    'tags' => ['زیست_شناسی'],
                    'children' => [],
                ],
                [
                    'name' => 'فیزیک',
                    'tags' => ['فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'شیمی',
                    'tags' => ['شیمی'],
                    'children' => [],
                ],
                [
                    'name' => 'نگارش',
                    'tags' => ['نگارش'],
                    'enable' => false,
                    'children' => [],
                ],
            ];
        $davazdahomE = $omoomi + [
                [
                    'name' => 'تاریخ',
                    'tags' => ['تاریخ'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'جامعه شناسی',
                    'tags' => ['جامعه_شناسی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'جغرافیا',
                    'tags' => ['جغرافیا'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'ریاضی و آمار',
                    'tags' => ['ریاضی_و_آمار'],
                    'children' => [],
                ],
                [
                    'name' => 'علوم و فنون ادبی',
                    'tags' => ['علوم_و_فنون_ادبی'],
                    'enable' => false,
                    'children' => [],
                ],
                [
                    'name' => 'فلسفه',
                    'tags' => ['فلسفه'],
                    'children' => [],
                ],
                [
                    'name' => 'نگارش',
                    'tags' => ['نگارش'],
                    'enable' => false,
                    'children' => [],
                ],
            ];

        $ghadimR = $omoomi + [
                [
                    'name' => 'دبفرانسیل',
                    'tags' => ['دبفرانسیل'],
                    'children' => [],
                ],
                [
                    'name' => 'تحلیلی',
                    'tags' => ['تحلیلی'],
                    'children' => [],
                ],
                [
                    'name' => 'گسسته',
                    'tags' => ['گسسته'],
                    'children' => [],
                ],
                [
                    'name' => 'حسابان',
                    'tags' => ['حسابان'],
                    'children' => [],
                ],
                [
                    'name' => 'جبر و احتمال',
                    'tags' => ['جبر_و_احتمال'],
                    'children' => [],
                ],
                [
                    'name' => 'ریاضی پایه',
                    'tags' => ['ریاضی_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'هندسه پایه',
                    'tags' => ['هندسه_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'فیزیک',
                    'tags' => ['فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'شیمی',
                    'tags' => ['شیمی'],
                    'children' => [],
                ],
                [
                    'name' => 'آمار و مدلسازی',
                    'tags' => ['آمار_و_مدلسازی'],
                    'children' => [],
                ],
                [
                    'name' => 'المپیاد نجوم',
                    'tags' => ['المپیاد_نجوم'],
                    'children' => [],
                ],
                [
                    'name' => 'المپیاد فیزیک',
                    'tags' => ['المپیاد_فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'اخلاق',
                    'tags' => ['اخلاق'],
                    'children' => [],
                ],
            ];
        $ghadimT = $omoomi + [
                [
                    'name' => 'زیست شناسی',
                    'tags' => ['زیست_شناسی'],
                    'children' => [],
                ],
                [
                    'name' => 'ریاضی تجربی',
                    'tags' => ['ریاضی_تجربی'],
                    'children' => [],
                ],
                [
                    'name' => 'ریاضی پایه',
                    'tags' => ['ریاضی_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'هندسه پایه',
                    'tags' => ['هندسه_پایه'],
                    'children' => [],
                ],
                [
                    'name' => 'فیزیک',
                    'tags' => ['فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'شیمی',
                    'tags' => ['شیمی'],
                    'children' => [],
                ],
                [
                    'name' => 'آمار و مدلسازی',
                    'tags' => ['آمار_و_مدلسازی'],
                    'children' => [],
                ],
                [
                    'name' => 'المپیاد نجوم',
                    'tags' => ['المپیاد_نجوم'],
                    'children' => [],
                ],
                [
                    'name' => 'المپیاد فیزیک',
                    'tags' => ['المپیاد_فیزیک'],
                    'children' => [],
                ],
                [
                    'name' => 'اخلاق',
                    'tags' => ['اخلاق'],
                    'children' => [],
                ],

            ];
        $ghadimE = $omoomi + [
                [
                    'name' => 'ریاضی انسانی',
                    'tags' => ['ریاضی_انسانی'],
                    'children' => [],
                ],
                [
                    'name' => 'ریاضی و آمار',
                    'tags' => ['ریاضی_و_آمار'],
                    'children' => [],
                ],
                [
                    'name' => 'منطق',
                    'tags' => ['منطق'],
                    'children' => [],
                ],
                [
                    'name' => 'آمار و مدلسازی',
                    'tags' => ['آمار_و_مدلسازی'],
                    'children' => [],
                ],
                [
                    'name' => 'اخلاق',
                    'tags' => ['اخلاق'],
                    'children' => [],
                ],
            ];

        $riazi = (new Riazi())->getTree();
        $tajrobi = (new Tajrobi())->getTree();
        $ensani = (new Ensani())->getTree();
        $reshteh = [
            [
                'name' => 'رشته تجربی',
                'tags' => ['رشته_تجربی'],
                'children' => $tajrobi,
            ],
            [
                'name' => 'رشته ریاضی',
                'tags' => ['رشته_ریاضی'],
                'children' => $riazi,
            ],
            [
                'name' => 'رشته انسانی',
                'tags' => ['رشته_انسانی'],
                'children' => $ensani,
            ],
        ];
        $paye = [
            [
                'name' => 'ابتدایی',
                'tags' => ['ابتدایی'],
                'enable' => false,
                'children' => [],
            ],
            [
                'name' => 'متوسطه1',
                'tags' => ['متوسطه1'],
                'enable' => false,
                'children' => [
                    [
                        'id' => '543',
                        'name' => 'هفتم',
                        'tags' => ['هفتم'],
                        'children' => [
                            [
                                'id' => '41',
                                'name' => 'ریاضی',
                                'tags' => ['ریاضی'],
                                'children' => [
                                    [
                                        'id' => '0',
                                        'name' => 'فصل 1: راهبرد‌های حل مسئله',
                                        'tags' => ['فصل_1:_راهبرد‌های_حل_مسئله'],
                                        'children' => [

                                        ],
                                    ],
                                    [
                                        'id' => '5',
                                        'name' => 'فصل 2: عددهای صحیح',
                                        'tags' => ['فصل_2:_عددهای_صحیح'],
                                        'children' => [
                                            [
                                                'id' => '1',
                                                'name' => 'معرفی عددهای علامت‌دار',
                                                'tags' => ['معرفی_عددهای_علامت‌دار'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '2',
                                                'name' => 'جمع و تفریق عددهای صحیح (1)',
                                                'tags' => ['جمع_و_تفریق_عددهای_صحیح_(1)'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '3',
                                                'name' => 'جمع و تفریق عددهای صحیح (2)',
                                                'tags' => ['جمع_و_تفریق_عددهای_صحیح_(2)'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '4',
                                                'name' => 'ضرب و تقسیم عددهای صحیح',
                                                'tags' => ['ضرب_و_تقسیم_عددهای_صحیح'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '10',
                                        'name' => 'فصل 3: جبر و معادله',
                                        'tags' => ['فصل_3:_جبر_و_معادله'],
                                        'children' => [
                                            [
                                                'id' => '6',
                                                'name' => 'الگوهای عددی',
                                                'tags' => ['الگوهای_عددی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '7',
                                                'name' => 'عبارت‌های جبری',
                                                'tags' => ['عبارت‌های_جبری'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '8',
                                                'name' => 'مقدار عددی یک عبارت جبری',
                                                'tags' => ['مقدار_عددی_یک_عبارت_جبری'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '9',
                                                'name' => 'معادله',
                                                'tags' => ['معادله'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '15',
                                        'name' => 'فصل 4: هندسه و استدلال',
                                        'tags' => ['فصل__4:_هندسه_و_استدلال'],
                                        'children' => [
                                            [
                                                'id' => '11',
                                                'name' => 'روابط بین پاره‌خط‌ها',
                                                'tags' => ['روابط_بین_پاره‌خط‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '12',
                                                'name' => 'روابط بین زاویه‌ها',
                                                'tags' => ['روابط_بین_زاویه‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '13',
                                                'name' => 'تبدیلات هندسی (انتقال، تقارن، دوران)',
                                                'tags' => ['تبدیلات_هندسی_(انتقال،_تقارن،_دوران)'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '14',
                                                'name' => 'شکل‌های مساوی (همنهشت)',
                                                'tags' => ['شکل‌های_مساوی_(همنهشت)'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '20',
                                        'name' => 'فصل 5: شمارنده‌ها و اعداد اول',
                                        'tags' => ['فصل_5:_شمارنده‌ها_و_اعداد_اول'],
                                        'children' => [
                                            [
                                                'id' => '16',
                                                'name' => 'عدد اول',
                                                'tags' => ['عدد_اول'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '17',
                                                'name' => 'شمارندۀ اول',
                                                'tags' => ['شمارندۀ_اول'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '18',
                                                'name' => 'بزرگ‌ترین شمارندۀ مشترک',
                                                'tags' => ['بزرگ‌ترین_شمارندۀ_مشترک'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '19',
                                                'name' => 'کوچک‌ترین مضرب مشترک',
                                                'tags' => ['کوچک‌ترین_مضرب_مشترک'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '25',
                                        'name' => 'فصل 6: سطح و حجم',
                                        'tags' => ['فصل_6:_سطح_و_حجم'],
                                        'children' => [
                                            [
                                                'id' => '21',
                                                'name' => 'حجم‌های هندسی',
                                                'tags' => ['حجم‌های_هندسی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '22',
                                                'name' => 'محاسبۀ حجم‌های منشوری',
                                                'tags' => ['محاسبۀ_حجم‌های_منشوری'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '23',
                                                'name' => 'مساحت جانبی و کل',
                                                'tags' => ['مساحت_جانبی_و_کل'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '24',
                                                'name' => 'حجم و سطح',
                                                'tags' => ['حجم_و_سطح'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '30',
                                        'name' => 'فصل 7: توان و جذر',
                                        'tags' => ['فصل_7:_توان_و_جذر'],
                                        'children' => [
                                            [
                                                'id' => '26',
                                                'name' => 'تعریف توان',
                                                'tags' => ['تعریف_توان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '27',
                                                'name' => 'محاسبۀ عبارت توان‌دار',
                                                'tags' => ['محاسبۀ_عبارت_توان‌دار'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '28',
                                                'name' => 'ساده‌کردن عبارت‌های توان‌دار',
                                                'tags' => ['ساده‌کردن_عبارت‌های_توان‌دار'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '29',
                                                'name' => 'جذر و ریشه',
                                                'tags' => ['جذر_و_ریشه'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '35',
                                        'name' => 'فصل 8: بردار و مختصات',
                                        'tags' => ['فصل_8:_بردار_و_مختصات'],
                                        'children' => [
                                            [
                                                'id' => '31',
                                                'name' => 'پاره‌خط جهت‌دار',
                                                'tags' => ['پاره‌خط_جهت‌دار'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '32',
                                                'name' => 'بردارهای مساوی و قرینه',
                                                'tags' => ['بردارهای_مساوی_و_قرینه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '33',
                                                'name' => 'مختصات',
                                                'tags' => ['مختصات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '34',
                                                'name' => 'بردار انتقال',
                                                'tags' => ['بردار_انتقال'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '40',
                                        'name' => 'فصل 9: آمار و احتمال',
                                        'tags' => ['فصل_9:_آمار_و_احتمال'],
                                        'children' => [
                                            [
                                                'id' => '36',
                                                'name' => 'جمع‌آوری و نمایش داده‌ها',
                                                'tags' => ['جمع‌آوری_و_نمایش_داده‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '37',
                                                'name' => 'نمودارها و تفسیر نتیجه‌ها',
                                                'tags' => ['نمودارها_و_تفسیر_نتیجه‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '38',
                                                'name' => 'احتمال یا اندازه‌گیری شانس',
                                                'tags' => ['احتمال_یا_اندازه‌گیری_شانس'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '39',
                                                'name' => 'احتمال و تجربه',
                                                'tags' => ['احتمال_و_تجربه'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '113',
                                'name' => 'زبان انگلیسی',
                                'tags' => ['زبان_انگلیسی'],
                                'children' => [
                                    [
                                        'id' => '49',
                                        'name' => 'Lesson 1: My Name',
                                        'tags' => ['Lesson_1:_My_Name'],
                                        'children' => [
                                            [
                                                'id' => '42',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '43',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '44',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '45',
                                                'name' => 'Listening',
                                                'tags' => ['Listening'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '46',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '47',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '48',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '57',
                                        'name' => 'Lesson 2: My Classmates',
                                        'tags' => ['Lesson_2:_My_Classmates'],
                                        'children' => [
                                            [
                                                'id' => '50',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '51',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '52',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '53',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '54',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '55',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '56',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '65',
                                        'name' => 'Lesson 3: My Age',
                                        'tags' => ['Lesson_3:_My_Age'],
                                        'children' => [
                                            [
                                                'id' => '58',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '59',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '60',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '61',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '62',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '63',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '64',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '73',
                                        'name' => 'Lesson 4: My Family',
                                        'tags' => ['Lesson_4:_My_Family'],
                                        'children' => [
                                            [
                                                'id' => '66',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '67',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '68',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '69',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '70',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '71',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '72',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '81',
                                        'name' => 'Lesson 5: My Appearance',
                                        'tags' => ['Lesson_5:_My_Appearance'],
                                        'children' => [
                                            [
                                                'id' => '74',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '75',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '76',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '77',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '78',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '79',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '80',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '89',
                                        'name' => 'Lesson 6: My House',
                                        'tags' => ['Lesson_6:_My_House'],
                                        'children' => [
                                            [
                                                'id' => '82',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '83',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '84',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '85',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '86',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '87',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '88',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '97',
                                        'name' => 'Lesson 7: My Address',
                                        'tags' => ['Lesson_7:_My_Address'],
                                        'children' => [
                                            [
                                                'id' => '90',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '91',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '92',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '93',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '94',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '95',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '96',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '105',
                                        'name' => 'Lesson 8: My Favorite Food',
                                        'tags' => ['Lesson_8:_My_Favorite_Food'],

                                        'children' => [
                                            [
                                                'id' => '98',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '99',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '100',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '101',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '102',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '103',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '104',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '112',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '106',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '107',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '108',
                                                'name' => 'Sounds & Letters',
                                                'tags' => ['Sounds_&_Letters'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '109',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '110',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '111',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '166',
                                'name' => 'عربی',
                                'tags' => ['عربی'],
                                'children' => [
                                    [
                                        'id' => '117',
                                        'name' => 'الدرس الأول: قیمة العلم، نور الکلام و کنز الکنوز',
                                        'tags' => ['الدرس_الأول:_قیمة_العلم،_نور_الکلام_و_کنز_الکنوز'],

                                        'children' => [
                                            [
                                                'id' => '114',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '115',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '116',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '121',
                                        'name' => 'الدرس الثانی: جواهر الکلام، کنوز الحکم و کنز النصیحة',
                                        'tags' => ['الدرس_الثانی:_جواهر_الکلام،_کنوز_الحکم_و_کنز_النصیحة'],

                                        'children' => [
                                            [
                                                'id' => '118',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '119',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '120',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '125',
                                        'name' => 'الدرس الثانی عشر: الأیام و الفصول و الالوان',
                                        'tags' => ['الدرس_الثانی_عشر:_الأیام_و_الفصول_و_الالوان'],

                                        'children' => [
                                            [
                                                'id' => '122',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '123',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '124',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '129',
                                        'name' => 'الدرس الثالث: الحکم النافعة و المواعظ العددیة',
                                        'tags' => ['الدرس_الثالث:_الحکم_النافعة_و_المواعظ_العددیة'],

                                        'children' => [
                                            [
                                                'id' => '126',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '127',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '128',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '133',
                                        'name' => 'الدرس الرابع: حوار بین ولدین',
                                        'tags' => ['الدرس_الرابع:_حوار_بین_ولدین'],

                                        'children' => [
                                            [
                                                'id' => '130',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '131',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '132',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '137',
                                        'name' => 'الدرس الخامس: فی السوق',
                                        'tags' => ['الدرس_الخامس:_فی_السوق'],
                                        'children' => [
                                            [
                                                'id' => '134',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '135',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '136',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '141',
                                        'name' => 'الدرس السادس: الجملات الذهبیة',
                                        'tags' => ['الدرس_السادس:_الجملات_الذهبیة'],

                                        'children' => [
                                            [
                                                'id' => '138',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '139',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '140',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '145',
                                        'name' => 'الدرس السابع: حوار فی الاسرة',
                                        'tags' => ['الدرس_السابع:_حوار_فی_الاسرة'],

                                        'children' => [
                                            [
                                                'id' => '142',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '143',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '144',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '149',
                                        'name' => 'الدرس الثامن: فی الحدود',
                                        'tags' => ['الدرس_الثامن:_فی_الحدود'],
                                        'children' => [
                                            [
                                                'id' => '146',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '147',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '148',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '153',
                                        'name' => 'الدرس التاسع: الأسرة الناجحة',
                                        'tags' => ['الدرس_التاسع:_الأسرة_الناجحة'],

                                        'children' => [
                                            [
                                                'id' => '150',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '151',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '152',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '157',
                                        'name' => 'الدرس العاشر: زینة الباطن',
                                        'tags' => ['الدرس_العاشر:_زینة_الباطن'],

                                        'children' => [
                                            [
                                                'id' => '154',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '155',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '156',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '161',
                                        'name' => 'الدرس الحادی عشر: الإخلاص فی العمل',
                                        'tags' => ['الدرس_الحادی_عشر:_الإخلاص_فی_العمل'],

                                        'children' => [
                                            [
                                                'id' => '158',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '159',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '160',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '165',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '162',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '163',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '164',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '256',
                                'name' => 'علوم',
                                'tags' => ['علوم'],
                                'children' => [
                                    [
                                        'id' => '167',
                                        'name' => 'فصل 1: تجربه و تفکر',
                                        'tags' => ['فصل_1:_تجربه_و_تفکر'],
                                        'children' => [

                                        ],
                                    ],
                                    [
                                        'id' => '174',
                                        'name' => 'فصل 2: اندازه‌گیری در علوم و ابزارهای آن',
                                        'tags' => ['فصل_2:_اندازه‌گیری_در_علوم_و_ابزارهای_آن'],

                                        'children' => [
                                            [
                                                'id' => '168',
                                                'name' => 'اندازه‌گیری و واحد‌های استاندارد',
                                                'tags' => ['اندازه‌گیری_و_واحد‌های_استاندارد'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '169',
                                                'name' => 'جرم و وزن',
                                                'tags' => ['جرم_و_وزن'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '170',
                                                'name' => 'طول، مساحت و حجم',
                                                'tags' => ['طول،_مساحت_و_حجم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '171',
                                                'name' => 'چگالی',
                                                'tags' => ['چگالی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '172',
                                                'name' => 'زمان',
                                                'tags' => ['زمان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '173',
                                                'name' => 'دقت در اندازه‌گیری',
                                                'tags' => ['دقت_در_اندازه‌گیری'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '181',
                                        'name' => 'فصل 3: اتم‌ها، الفبای مواد',
                                        'tags' => ['فصل_3:_اتم‌ها،_الفبای_مواد'],

                                        'children' => [
                                            [
                                                'id' => '175',
                                                'name' => 'مواد در زندگی ما',
                                                'tags' => ['مواد_در_زندگی_ما'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '176',
                                                'name' => 'عنصر‌ها و ترکیب‌ها',
                                                'tags' => ['عنصر‌ها_و_ترکیب‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '177',
                                                'name' => 'ویژگی‌های فلزات و نافلزات',
                                                'tags' => ['ویژگی‌های_فلزات_و_نافلزات'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '178',
                                                'name' => 'ذرات تشکیل‌دهندۀ اتم',
                                                'tags' => ['ذرات_تشکیل‌دهندۀ_اتم'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '179',
                                                'name' => 'تراکم‌پذیری و انبساط مواد',
                                                'tags' => ['تراکم‌پذیری_و_انبساط_مواد'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '180',
                                                'name' => 'گرما و تغییر حالت ماده',
                                                'tags' => ['گرما_و_تغییر_حالت_ماده'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '187',
                                        'name' => 'فصل 4: مواد پیرامون ما',
                                        'tags' => ['فصل_4:_مواد_پیرامون_ما'],
                                        'children' => [
                                            [
                                                'id' => '182',
                                                'name' => 'مواد طبیعی و مصنوعی',
                                                'tags' => ['مواد_طبیعی_و_مصنوعی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '183',
                                                'name' => 'ویژگی‌های مواد',
                                                'tags' => ['ویژگی‌های_مواد'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '184',
                                                'name' => 'کاربرد مواد',
                                                'tags' => ['کاربرد_مواد'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '185',
                                                'name' => 'آلیاژها',
                                                'tags' => ['آلیاژها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '186',
                                                'name' => 'مواد هوشمند',
                                                'tags' => ['مواد_هوشمند'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '193',
                                        'name' => 'فصل 5: از معدن تا خانه',
                                        'tags' => ['فصل_5:_از_معدن_تا_خانه'],
                                        'children' => [
                                            [
                                                'id' => '188',
                                                'name' => 'اندوخته‌های زمین',
                                                'tags' => ['اندوخته‌های_زمین'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '189',
                                                'name' => 'چگونه می‌توان به آهن دست یافت؟',
                                                'tags' => ['چگونه_می‌توان_به_آهن_دست_یافت؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '190',
                                                'name' => 'به دنبال سرپناهی ایمن',
                                                'tags' => ['به_دنبال_سرپناهی_ایمن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '191',
                                                'name' => 'اندوخته‌های طبیعی و ظروف آشپزخانه',
                                                'tags' => ['اندوخته‌های_طبیعی_و_ظروف_آشپزخانه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '192',
                                                'name' => 'سرعت مصرف منابع و راه‌های محافظت از آن',
                                                'tags' => ['سرعت_مصرف_منابع_و_راه‌های_محافظت_از_آن'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '200',
                                        'name' => 'فصل 6: سفر آب روی زمین',
                                        'tags' => ['فصل_6:_سفر_آب_روی_زمین'],
                                        'children' => [
                                            [
                                                'id' => '194',
                                                'name' => 'آب، فراوان اما کمیاب',
                                                'tags' => ['آب،_فراوان_اما_کمیاب'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '195',
                                                'name' => 'آب‌های جاری',
                                                'tags' => ['آب‌های_جاری'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '196',
                                                'name' => 'دریاچه‌ها',
                                                'tags' => ['دریاچه‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '197',
                                                'name' => 'دریاها و اقیانوس‌ها',
                                                'tags' => ['دریاها_و_اقیانوس‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '198',
                                                'name' => 'باران چگونه تشکیل و به کجا می‌رود؟',
                                                'tags' => ['باران_چگونه_تشکیل_و_به_کجا_می‌رود؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '199',
                                                'name' => 'یخچال‌ها',
                                                'tags' => ['یخچال‌ها'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '204',
                                        'name' => 'فصل 7: سفر آب درون زمین',
                                        'tags' => ['فصل_7:_سفر_آب_درون_زمین'],
                                        'children' => [
                                            [
                                                'id' => '201',
                                                'name' => 'آب‌های زیر‌زمینی و عوامل مؤثر در آن',
                                                'tags' => ['آب‌های_زیر‌زمینی_و_عوامل_مؤثر_در_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '202',
                                                'name' => 'سفره‌های آب زیرزمینی (آبخوان) و ویژگی‌های آن',
                                                'tags' => ['سفره‌های_آب_زیرزمینی_(آبخوان)_و_ویژگی‌های_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '203',
                                                'name' => 'قنات - چرخۀ آب',
                                                'tags' => ['قنات_-_چرخۀ_آب'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '210',
                                        'name' => 'فصل 8: انرژی و تبدیل‌های آن',
                                        'tags' => ['فصل_8:_انرژی_و_تبدیل‌های_آن'],

                                        'children' => [
                                            [
                                                'id' => '205',
                                                'name' => 'کار',
                                                'tags' => ['کار'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '206',
                                                'name' => 'انرژی جنبشی',
                                                'tags' => ['انرژی_جنبشی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '207',
                                                'name' => 'انرژی پتانسیل',
                                                'tags' => ['انرژی_پتانسیل'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '208',
                                                'name' => 'اصل پایستگی انرژی و تبدیلات انرژی',
                                                'tags' => ['اصل_پایستگی_انرژی_و_تبدیلات_انرژی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '209',
                                                'name' => 'انرژی شیمیایی مواد غذایی و آهنگ مصرف انرژی',
                                                'tags' => ['انرژی_شیمیایی_مواد_غذایی_و_آهنگ_مصرف_انرژی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '213',
                                        'name' => 'فصل 9: منابع انرژی',
                                        'tags' => ['فصل_9:_منابع_انرژی'],
                                        'children' => [
                                            [
                                                'id' => '211',
                                                'name' => 'منابع تجدید ناپذیر',
                                                'tags' => ['منابع_تجدید_ناپذیر'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '212',
                                                'name' => 'منابع تجدید پذیر',
                                                'tags' => ['منابع_تجدید_پذیر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '219',
                                        'name' => 'فصل 10: گرما و مصرف انرژی',
                                        'tags' => ['فصل_10:_گرما_و_مصرف_انرژی'],

                                        'children' => [
                                            [
                                                'id' => '214',
                                                'name' => 'دما و دماسنجی',
                                                'tags' => ['دما_و_دماسنجی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '215',
                                                'name' => 'انواع دماسنج‌ها',
                                                'tags' => ['انواع_دماسنج‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '216',
                                                'name' => 'تعریف گرما - دمای تعادل',
                                                'tags' => ['تعریف_گرما_-_دمای_تعادل'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '217',
                                                'name' => 'روش‌های انتقال گرما',
                                                'tags' => ['روش‌های_انتقال_گرما'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '218',
                                                'name' => 'کاربرد‌های مربوط به گرما',
                                                'tags' => ['کاربرد‌های_مربوط_به_گرما'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '224',
                                        'name' => 'فصل 11: یاخته‌ها و سازمان‌بندی آن',
                                        'tags' => ['فصل_11:_یاخته‌ها_و_سازمان‌بندی_آن'],

                                        'children' => [
                                            [
                                                'id' => '220',
                                                'name' => 'یاخته، کوچک‌ترین واحد زنده',
                                                'tags' => ['یاخته،_کوچک‌ترین_واحد_زنده'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '221',
                                                'name' => 'نگاهی به درون یاخته و شباهت‌های آن',
                                                'tags' => ['نگاهی_به_درون_یاخته_و_شباهت‌های_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '222',
                                                'name' => 'مقایسۀ یاخته‌های گیاهی و جانوری',
                                                'tags' => ['مقایسۀ_یاخته‌های_گیاهی_و_جانوری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '223',
                                                'name' => 'سازمان‌بندی یاخته‌ها',
                                                'tags' => ['سازمان‌بندی_یاخته‌ها'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '233',
                                        'name' => 'فصل 12: سفرۀ سلامت',
                                        'tags' => ['فصل_12:_سفرۀ_سلامت'],
                                        'children' => [
                                            [
                                                'id' => '225',
                                                'name' => 'موادی که غذاها دارند',
                                                'tags' => ['موادی_که_غذاها_دارند'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '226',
                                                'name' => 'کربوهیدرات‌ها (قندها)',
                                                'tags' => ['کربوهیدرات‌ها_(قندها)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '227',
                                                'name' => 'لیپید‌ها (چربی‌ها)',
                                                'tags' => ['لیپید‌ها_(چربی‌ها)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '228',
                                                'name' => 'پروتئین‌ها',
                                                'tags' => ['پروتئین‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '229',
                                                'name' => 'ویتامین‌ها',
                                                'tags' => ['ویتامین‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '230',
                                                'name' => 'مواد معدنی',
                                                'tags' => ['مواد_معدنی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '231',
                                                'name' => 'آب',
                                                'tags' => ['آب'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '232',
                                                'name' => 'تغذیۀ سالم',
                                                'tags' => ['تغذیۀ_سالم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '240',
                                        'name' => 'فصل 13: سفر غذا',
                                        'tags' => ['فصل_13:_سفر_غذا'],
                                        'children' => [
                                            [
                                                'id' => '234',
                                                'name' => 'گوارش غذا',
                                                'tags' => ['گوارش_غذا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '235',
                                                'name' => 'لولۀ گوارش و غدد گوارشی',
                                                'tags' => ['لولۀ_گوارش_و_غدد_گوارشی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '236',
                                                'name' => 'از دهان تا معده',
                                                'tags' => ['از_دهان_تا_معده'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '237',
                                                'name' => 'رودۀ باریک',
                                                'tags' => ['رودۀ_باریک'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '238',
                                                'name' => 'رودۀ بزرگ',
                                                'tags' => ['رودۀ_بزرگ'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '239',
                                                'name' => 'کبد',
                                                'tags' => ['کبد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '247',
                                        'name' => 'فصل 14: گردش مواد',
                                        'tags' => ['فصل_14:_گردش_مواد'],
                                        'children' => [
                                            [
                                                'id' => '241',
                                                'name' => 'رابطه بین همه دستگاه‌های بدن',
                                                'tags' => ['رابطه_بین_همه_دستگاه‌های_بدن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '242',
                                                'name' => 'قلب',
                                                'tags' => ['قلب'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '243',
                                                'name' => 'رگ‌های قلب - رگ‌های بدن',
                                                'tags' => ['رگ‌های_قلب_-_رگ‌های_بدن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '244',
                                                'name' => 'گردش کوچک و بزرگ خون',
                                                'tags' => ['گردش_کوچک_و_بزرگ_خون'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '245',
                                                'name' => 'فشار خون و نبض',
                                                'tags' => ['فشار_خون_و_نبض'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '246',
                                                'name' => 'خون',
                                                'tags' => ['خون'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '255',
                                        'name' => 'فصل 15: تبادل با محیط',
                                        'tags' => ['فصل_15:_تبادل_با_محیط'],
                                        'children' => [
                                            [
                                                'id' => '251',
                                                'name' => 'دستگاه تنفس',
                                                'tags' => ['دستگاه_تنفس'],
                                                'children' => [
                                                    [
                                                        'id' => '248',
                                                        'name' => 'ساختار دستگاه تنفس',
                                                        'tags' => ['ساختار_دستگاه_تنفس'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '249',
                                                        'name' => 'تبادل هوا - تولید صدا',
                                                        'tags' => ['تبادل_هوا_-_تولید_صدا'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '250',
                                                        'name' => 'انتقال گازها',
                                                        'tags' => ['انتقال_گازها'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],
                                            [
                                                'id' => '254',
                                                'name' => 'دستگاه دفع ادرار',
                                                'tags' => ['دستگاه_دفع_ادرار'],
                                                'children' => [
                                                    [
                                                        'id' => '252',
                                                        'name' => 'چگونگی کار کلیه',
                                                        'tags' => ['چگونگی_کار_کلیه'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '253',
                                                        'name' => 'تنظیم محیط داخلی',
                                                        'tags' => ['تنظیم_محیط_داخلی'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '394',
                                'name' => 'فارسی',
                                'tags' => ['فارسی'],
                                'children' => [
                                    [
                                        'id' => '263',
                                        'name' => 'درس اول: زنگ آفرینش',
                                        'tags' => ['درس_اول:_زنگ_آفرینش'],
                                        'children' => [
                                            [
                                                'id' => '257',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '258',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '259',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '260',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '261',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '262',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '270',
                                        'name' => 'درس دوم: چشمۀ معرفت',
                                        'tags' => ['درس_دوم:_چشمۀ_معرفت'],
                                        'children' => [
                                            [
                                                'id' => '264',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '265',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '266',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '267',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '268',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '269',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '278',
                                        'name' => 'درس سوم: نسل آینده‌ساز',
                                        'tags' => ['درس_سوم:_نسل_آینده‌ساز'],
                                        'children' => [
                                            [
                                                'id' => '271',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '272',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '273',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '274',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '275',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '276',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '277',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '285',
                                        'name' => 'درس چهارم: با بهاری که می‌رسد از راه، زیبایی شکفتن',
                                        'tags' => ['درس_چهارم:_با_بهاری_که_می‌رسد_از_راه،_زیبایی_شکفتن'],

                                        'children' => [
                                            [
                                                'id' => '279',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '280',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '281',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '282',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '283',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '284',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '292',
                                        'name' => 'درس ششم: قلب کوچکم را به چه‌کسی بدهم؟',
                                        'tags' => ['درس_ششم:_قلب_کوچکم_را_به_چه‌کسی_بدهم؟'],

                                        'children' => [
                                            [
                                                'id' => '286',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '287',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '288',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '289',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '290',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '291',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '299',
                                        'name' => 'درس هفتم: علم زندگانی',
                                        'tags' => ['درس_هفتم:_علم_زندگانی'],
                                        'children' => [
                                            [
                                                'id' => '293',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '294',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '295',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '296',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '297',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '298',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '306',
                                        'name' => 'درس هشتم: زندگی همین لحظه‌‎هاست',
                                        'tags' => ['درس_هشتم:_زندگی_همین_لحظه‌‎هاست'],

                                        'children' => [
                                            [
                                                'id' => '300',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '301',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '302',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '303',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '304',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '305',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '313',
                                        'name' => 'درس نهم: نصیحت امام (ره)، شوق خواندن',
                                        'tags' => ['درس_نهم:_نصیحت_امام_(ره)،_شوق_خواندن'],

                                        'children' => [
                                            [
                                                'id' => '307',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '308',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '309',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '310',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '311',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '312',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '321',
                                        'name' => 'درس دهم: کلاس ادبیات، مرواریدی در صدف، زندگی حسابی، فرزند انقلاب',
                                        'tags' => ['درس_دهم:_کلاس_ادبیات،_مرواریدی_در_صدف،_زندگی_حسابی،_فرزند_انقلاب'],

                                        'children' => [
                                            [
                                                'id' => '314',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '315',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '316',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '317',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '318',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '319',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '320',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '328',
                                        'name' => 'درس یازدهم: عهد و پیمان، عشق به مردم، رفتار بهشتی، گرمای محبت',
                                        'tags' => ['درس_یازدهم:_عهد_و_پیمان،_عشق_به_مردم،_رفتار_بهشتی،_گرمای_محبت'],

                                        'children' => [
                                            [
                                                'id' => '322',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '323',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '324',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '325',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '326',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '327',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '336',
                                        'name' => 'درس دوازدهم: خدمات متقابل اسلام و ایران',
                                        'tags' => ['درس_دوازدهم:_خدمات_متقابل_اسلام_و_ایران'],

                                        'children' => [
                                            [
                                                'id' => '329',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '330',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '331',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '332',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '333',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '334',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '335',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '343',
                                        'name' => 'درس سیزدهم: اسوۀ نیکو',
                                        'tags' => ['درس_سیزدهم:_اسوۀ_نیکو'],
                                        'children' => [
                                            [
                                                'id' => '337',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '338',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '339',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '340',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '341',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '342',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '350',
                                        'name' => 'درس چهاردهم: امام خمینی (ره)',
                                        'tags' => ['درس_چهاردهم:_امام_خمینی_(ره)'],

                                        'children' => [
                                            [
                                                'id' => '344',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '345',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '346',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '347',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '348',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '349',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '357',
                                        'name' => 'درس پانزدهم: روان‌خوانی: چرا زبان فارسی را دوست دارم؟',
                                        'tags' => ['درس_پانزدهم:_روان‌خوانی:_چرا_زبان_فارسی_را_دوست_دارم؟'],

                                        'children' => [
                                            [
                                                'id' => '351',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '352',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '353',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '354',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '355',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '356',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '364',
                                        'name' => 'درس شانزدهم: آدم‌آهنی و شاپرک',
                                        'tags' => ['درس_شانزدهم:_آدم‌آهنی_و_شاپرک'],

                                        'children' => [
                                            [
                                                'id' => '358',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '359',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '360',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '361',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '362',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '363',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '371',
                                        'name' => 'درس هفدهم: ما‌ می‌توانیم',
                                        'tags' => ['درس_هفدهم:_ما‌_می‌توانیم'],
                                        'children' => [
                                            [
                                                'id' => '365',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '366',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '367',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '368',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '369',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '370',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '378',
                                        'name' => 'ستایش: یاد تو',
                                        'tags' => ['ستایش:_یاد_تو'],
                                        'children' => [
                                            [
                                                'id' => '372',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '373',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '374',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '375',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '376',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '377',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '385',
                                        'name' => 'نیایش',
                                        'tags' => ['نیایش'],
                                        'children' => [
                                            [
                                                'id' => '379',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '380',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '381',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '382',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '383',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '384',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '393',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '386',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '387',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '388',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '389',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '390',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '391',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '392',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '493',
                                'name' => 'مطالعات اجتماعی',
                                'tags' => ['مطالعات_اجتماعی'],
                                'children' => [
                                    [
                                        'id' => '397',
                                        'name' => 'درس 1: من حق دارم',
                                        'tags' => ['درس_1:_من_حق_دارم'],
                                        'children' => [
                                            [
                                                'id' => '395',
                                                'name' => 'تعریف حق',
                                                'tags' => ['تعریف_حق'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '396',
                                                'name' => 'حقوق افراد در محیط‌های گوناگون',
                                                'tags' => ['حقوق_افراد_در_محیط‌های_گوناگون'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '398',
                                        'name' => 'درس 3: چرا به مقررات و قوانین نیاز داریم؟',
                                        'tags' => ['درس_3:_چرا_به_مقررات_و_قوانین_نیاز_داریم؟'],

                                        'children' => [

                                        ],
                                    ],
                                    [
                                        'id' => '404',
                                        'name' => 'درس 4: قانونگذاری',
                                        'tags' => ['درس_4:_قانونگذاری'],
                                        'children' => [
                                            [
                                                'id' => '399',
                                                'name' => 'تعریف قانون و انواع آن',
                                                'tags' => ['تعریف_قانون_و_انواع_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '400',
                                                'name' => 'قانون اساسی',
                                                'tags' => ['قانون_اساسی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '403',
                                                'name' => 'قوۀ مقننه',
                                                'tags' => ['قوۀ_مقننه'],
                                                'children' => [
                                                    [
                                                        'id' => '401',
                                                        'name' => 'مجلس شورای اسلامی',
                                                        'tags' => ['مجلس_شورای_اسلامی'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '402',
                                                        'name' => 'شورای نگهبان',
                                                        'tags' => ['شورای_نگهبان'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '407',
                                        'name' => 'درس 5: همدلی و همیاری در حوادث',
                                        'tags' => ['درس_5:_همدلی_و_همیاری_در_حوادث'],

                                        'children' => [
                                            [
                                                'id' => '405',
                                                'name' => 'تعریف همدلی و همیاری',
                                                'tags' => ['تعریف_همدلی_و_همیاری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '406',
                                                'name' => 'کدام مؤسسات اجتماعی در حوادث به‌ ما کمک می‌کنند؟',
                                                'tags' => ['کدام_مؤسسات_اجتماعی_در_حوادث_به‌_ما_کمک_می‌کنند؟'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '410',
                                        'name' => 'درس 6: بیمه و مقابله با حوادث',
                                        'tags' => ['درس_6:_بیمه_و_مقابله_با_حوادث'],

                                        'children' => [
                                            [
                                                'id' => '408',
                                                'name' => 'بیمه چیست و چرا به وجود آمده است؟',
                                                'tags' => ['بیمه_چیست_و_چرا_به_وجود_آمده_است؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '409',
                                                'name' => 'انواع بیمه',
                                                'tags' => ['انواع_بیمه'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '413',
                                        'name' => 'درس 7: تولید و توزیع',
                                        'tags' => ['درس_7:_تولید_و_توزیع'],
                                        'children' => [
                                            [
                                                'id' => '411',
                                                'name' => 'تولید و انواع آن',
                                                'tags' => ['تولید_و_انواع_آن'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '412',
                                                'name' => 'توزیع کالا و خدمات',
                                                'tags' => ['توزیع_کالا_و_خدمات'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '416',
                                        'name' => 'درس 8: مصرف',
                                        'tags' => ['درس_8:_مصرف'],
                                        'children' => [
                                            [
                                                'id' => '414',
                                                'name' => 'مصرف‌کننده و حقوق او',
                                                'tags' => ['مصرف‌کننده_و_حقوق_او'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '415',
                                                'name' => 'مسئولیت‌های مصرف‌کننده',
                                                'tags' => ['مسئولیت‌های_مصرف‌کننده'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '420',
                                        'name' => 'درس 9: من کجا زندگی می‌کنم؟',
                                        'tags' => ['درس_9:_من_کجا_زندگی_می‌کنم؟'],

                                        'children' => [
                                            [
                                                'id' => '417',
                                                'name' => 'ویژگی‌های طبیعی و انسانی',
                                                'tags' => ['ویژگی‌های_طبیعی_و_انسانی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '418',
                                                'name' => 'ویژگی‌های جغرافیایی هر مکان',
                                                'tags' => ['ویژگی‌های_جغرافیایی_هر_مکان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '419',
                                                'name' => 'چه وسایلی به شناخت محیط زندگی کمک می‌کنند',
                                                'tags' => ['چه_وسایلی_به_شناخت_محیط_زندگی_کمک_می‌کنند'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '425',
                                        'name' => 'درس 10: ایران، خانۀ ما',
                                        'tags' => ['درس_10:_ایران،_خانۀ_ما'],
                                        'children' => [
                                            [
                                                'id' => '421',
                                                'name' => 'تقسیمات کشوری ایران',
                                                'tags' => ['تقسیمات_کشوری_ایران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '424',
                                                'name' => 'اشکال زمین در ایران',
                                                'tags' => ['اشکال_زمین_در_ایران'],

                                                'children' => [
                                                    [
                                                        'id' => '422',
                                                        'name' => 'نواحی مرتفع و بلند',
                                                        'tags' => ['نواحی_مرتفع_و_بلند'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '423',
                                                        'name' => 'نواحی پست و هموار',
                                                        'tags' => ['نواحی_پست_و_هموار'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '428',
                                        'name' => 'درس 11: تنوع آب‌و‌هوای ایران',
                                        'tags' => ['درس_11:_تنوع_آب‌و‌هوای_ایران'],

                                        'children' => [
                                            [
                                                'id' => '426',
                                                'name' => 'محیط طبیعی ایران متنوع است',
                                                'tags' => ['محیط_طبیعی_ایران_متنوع_است'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '427',
                                                'name' => 'آب‌و‌هوای ایران',
                                                'tags' => ['آب‌و‌هوای_ایران'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '434',
                                        'name' => 'درس 12: حفاظت از زیستگاه‌های ایران',
                                        'tags' => ['درس_12:_حفاظت_از_زیستگاه‌های_ایران'],

                                        'children' => [
                                            [
                                                'id' => '429',
                                                'name' => 'گونه‌های گیاهی و جانوری ایران',
                                                'tags' => ['گونه‌های_گیاهی_و_جانوری_ایران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '430',
                                                'name' => 'چرا زیستگاه‌ها تخریب می‌شوند؟',
                                                'tags' => ['چرا_زیستگاه‌ها_تخریب_می‌شوند؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '431',
                                                'name' => 'چرا از زیستگاه‌ها حفاظت می‌کنیم؟',
                                                'tags' => ['چرا_از_زیستگاه‌ها_حفاظت_می‌کنیم؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '432',
                                                'name' => 'چگونه از زیستگاه‌ها حفاظت می‌کنیم؟',
                                                'tags' => ['چگونه_از_زیستگاه‌ها_حفاظت_می‌کنیم؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '433',
                                                'name' => 'مناطق حفاظت‌شده',
                                                'tags' => ['مناطق_حفاظت‌شده'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '438',
                                        'name' => 'درس 13: جمعیت ایران',
                                        'tags' => ['درس_13:_جمعیت_ایران'],
                                        'children' => [
                                            [
                                                'id' => '435',
                                                'name' => 'سرشماری جمعیت ایران',
                                                'tags' => ['سرشماری_جمعیت_ایران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '436',
                                                'name' => 'جمعیت چگونه افزایش می‌یابد؟',
                                                'tags' => ['جمعیت_چگونه_افزایش_می‌یابد؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '437',
                                                'name' => 'تراکم جمعیت و پراکندگی آن',
                                                'tags' => ['تراکم_جمعیت_و_پراکندگی_آن'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '445',
                                        'name' => 'درس 14: منابع آب و خاک',
                                        'tags' => ['درس_14:_منابع_آب_و_خاک'],
                                        'children' => [
                                            [
                                                'id' => '441',
                                                'name' => 'آب',
                                                'tags' => ['آب'],
                                                'children' => [
                                                    [
                                                        'id' => '439',
                                                        'name' => 'منابع آب در ایران',
                                                        'tags' => ['منابع_آب_در_ایران'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '440',
                                                        'name' => 'مصرف آب',
                                                        'tags' => ['مصرف_آب'],
                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],
                                            [
                                                'id' => '444',
                                                'name' => 'خاک',
                                                'tags' => ['خاک'],
                                                'children' => [
                                                    [
                                                        'id' => '442',
                                                        'name' => 'خاک چگونه تشکیل می‌شود؟',
                                                        'tags' => ['خاک_چگونه_تشکیل_می‌شود؟'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '443',
                                                        'name' => 'عوامل ازبین‌رفتن خاک',
                                                        'tags' => ['عوامل_ازبین‌رفتن_خاک'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '448',
                                        'name' => 'درس 15: گردشگری چیست؟',
                                        'tags' => ['درس_15:_گردشگری_چیست؟'],
                                        'children' => [
                                            [
                                                'id' => '446',
                                                'name' => 'گردشگری و انواع آن',
                                                'tags' => ['گردشگری_و_انواع_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '447',
                                                'name' => 'گردشگری و نقشه',
                                                'tags' => ['گردشگری_و_نقشه'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '452',
                                        'name' => 'درس 16: جاذبه‌های گردشگری ایران',
                                        'tags' => ['درس_16:_جاذبه‌های_گردشگری_ایران'],

                                        'children' => [
                                            [
                                                'id' => '449',
                                                'name' => 'سفرهای زیارتی',
                                                'tags' => ['سفرهای_زیارتی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '450',
                                                'name' => 'گردشگری تاریخی',
                                                'tags' => ['گردشگری_تاریخی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '451',
                                                'name' => 'طبیعت‌گردی و حفاظت از طبیعت',
                                                'tags' => ['طبیعت‌گردی_و_حفاظت_از_طبیعت'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '456',
                                        'name' => 'درس 17: میراث فرهنگی و تاریخ',
                                        'tags' => ['درس_17:_میراث_فرهنگی_و_تاریخ'],

                                        'children' => [
                                            [
                                                'id' => '453',
                                                'name' => 'میراث فرهنگی و حفاظت از آن',
                                                'tags' => ['میراث_فرهنگی_و_حفاظت_از_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '454',
                                                'name' => 'چه کسانی گذشته را مطالعه می‌کنند؟',
                                                'tags' => ['چه_کسانی_گذشته_را_مطالعه_می‌کنند؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '455',
                                                'name' => 'موزه‌ها و زمان میراث فرهنگی',
                                                'tags' => ['موزه‌ها_و_زمان_میراث_فرهنگی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '457',
                                        'name' => 'درس 18: قدیمی‌ترین سکونتگاه‌های ایران',
                                        'tags' => ['درس_18:_قدیمی‌ترین_سکونتگاه‌های_ایران'],

                                        'children' => [

                                        ],
                                    ],
                                    [
                                        'id' => '464',
                                        'name' => 'درس 19: آریایی‌ها و تشکیل حکومت‌های قدرتمند در ایران',
                                        'tags' => ['درس_19:_آریایی‌ها_و_تشکیل_حکومت‌های_قدرتمند_در_ایران'],

                                        'children' => [
                                            [
                                                'id' => '458',
                                                'name' => 'آریایی‌ها',
                                                'tags' => ['آریایی‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '459',
                                                'name' => 'مادها',
                                                'tags' => ['مادها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '460',
                                                'name' => 'هخامنشیان',
                                                'tags' => ['هخامنشیان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '461',
                                                'name' => 'سلوکیان',
                                                'tags' => ['سلوکیان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '462',
                                                'name' => 'اشکانیان',
                                                'tags' => ['اشکانیان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '463',
                                                'name' => 'ساسانیان',
                                                'tags' => ['ساسانیان'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '469',
                                        'name' => 'درس 20: امپراتوری‌های ایران باستان چگونه کشور را اداره می‌کردند؟',
                                        'tags' => ['درس_20:_امپراتوری‌های_ایران_باستان_چگونه_کشور_را_اداره_می‌کردند؟'],

                                        'children' => [
                                            [
                                                'id' => '465',
                                                'name' => 'نوع حکومت',
                                                'tags' => ['نوع_حکومت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '466',
                                                'name' => 'مقام‌های حکومتی',
                                                'tags' => ['مقام‌های_حکومتی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '467',
                                                'name' => 'تقسیمات کشوری و پایتخت‌ها',
                                                'tags' => ['تقسیمات_کشوری_و_پایتخت‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '468',
                                                'name' => 'سپاه و قدرت نظامی',
                                                'tags' => ['سپاه_و_قدرت_نظامی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '473',
                                        'name' => 'درس 21: اوضاع اجتماعی ایران باستان',
                                        'tags' => ['درس_21:_اوضاع_اجتماعی_ایران_باستان'],

                                        'children' => [
                                            [
                                                'id' => '470',
                                                'name' => 'خانواده',
                                                'tags' => ['خانواده'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '471',
                                                'name' => 'زندگی شهری و روستایی',
                                                'tags' => ['زندگی_شهری_و_روستایی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '472',
                                                'name' => 'نابرابری اجتماعی',
                                                'tags' => ['نابرابری_اجتماعی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '478',
                                        'name' => 'درس 22: اوضاع اقتصادی در ایران باستان',
                                        'tags' => ['درس_22:_اوضاع_اقتصادی_در_ایران_باستان'],

                                        'children' => [
                                            [
                                                'id' => '474',
                                                'name' => 'کشاورزی و دامپروری',
                                                'tags' => ['کشاورزی_و_دامپروری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '475',
                                                'name' => 'صنعت',
                                                'tags' => ['صنعت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '476',
                                                'name' => 'تجارت و ضرب سکه',
                                                'tags' => ['تجارت_و_ضرب_سکه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '477',
                                                'name' => 'درآمد‌ها و مخارج حکومت',
                                                'tags' => ['درآمد‌ها_و_مخارج_حکومت'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '484',
                                        'name' => 'درس 23: عقاید و سبک زندگی مردم در ایران باستان',
                                        'tags' => ['درس_23:_عقاید_و_سبک_زندگی_مردم_در_ایران_باستان'],

                                        'children' => [
                                            [
                                                'id' => '479',
                                                'name' => 'دین',
                                                'tags' => ['دین'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '480',
                                                'name' => 'تغذیه و آداب غذا خوردن',
                                                'tags' => ['تغذیه_و_آداب_غذا_خوردن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '481',
                                                'name' => 'پوشاک',
                                                'tags' => ['پوشاک'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '482',
                                                'name' => 'ورزش',
                                                'tags' => ['ورزش'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '483',
                                                'name' => 'جشن‌ها',
                                                'tags' => ['جشن‌ها'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '489',
                                        'name' => 'درس 24: دانش و هنر در ایران باستان',
                                        'tags' => ['درس_24:_دانش_و_هنر_در_ایران_باستان'],

                                        'children' => [
                                            [
                                                'id' => '485',
                                                'name' => 'زبان فارسی',
                                                'tags' => ['زبان_فارسی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '486',
                                                'name' => 'خط',
                                                'tags' => ['خط'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '487',
                                                'name' => 'دانش',
                                                'tags' => ['دانش'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '488',
                                                'name' => 'هنر و معماری',
                                                'tags' => ['هنر_و_معماری'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '492',
                                        'name' => 'درس 2: من مسئول هستم',
                                        'tags' => ['درس_2:_من_مسئول_هستم'],
                                        'children' => [
                                            [
                                                'id' => '490',
                                                'name' => 'حقوق متقابل و مسئولیت‌ها',
                                                'tags' => ['حقوق_متقابل_و_مسئولیت‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '491',
                                                'name' => 'مسئولیت‌های گوناگون',
                                                'tags' => ['مسئولیت‌های_گوناگون'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '542',
                                'name' => 'پیام‌های آسمان',
                                'tags' => ['پیام‌های_آسمان'],
                                'children' => [
                                    [
                                        'id' => '496',
                                        'name' => 'درس اول: بینای مهربان',
                                        'tags' => ['درس_اول:_بینای_مهربان'],
                                        'children' => [
                                            [
                                                'id' => '494',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '495',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '499',
                                        'name' => 'درس دوم: استعانت از خداوند',
                                        'tags' => ['درس_دوم:_استعانت_از_خداوند'],

                                        'children' => [
                                            [
                                                'id' => '497',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '498',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '502',
                                        'name' => 'درس سوم: تلخ یا شیرین',
                                        'tags' => ['درس_سوم:_تلخ_یا_شیرین'],
                                        'children' => [
                                            [
                                                'id' => '500',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '501',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '505',
                                        'name' => 'درس چهارم: عبور آسان',
                                        'tags' => ['درس_چهارم:_عبور_آسان'],
                                        'children' => [
                                            [
                                                'id' => '503',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '504',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '508',
                                        'name' => 'درس پنجم: پیامبر رحمت',
                                        'tags' => ['درس_پنجم:_پیامبر_رحمت'],
                                        'children' => [
                                            [
                                                'id' => '506',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '507',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '511',
                                        'name' => 'درس ششم: اسوۀ فداکاری و عدالت',
                                        'tags' => ['درس_ششم:_اسوۀ_فداکاری_و_عدالت'],

                                        'children' => [
                                            [
                                                'id' => '509',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '510',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '514',
                                        'name' => 'درس هفتم: برترین بانو',
                                        'tags' => ['درس_هفتم:_برترین_بانو'],
                                        'children' => [
                                            [
                                                'id' => '512',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '513',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '517',
                                        'name' => 'درس هشتم: افتخار بندگی',
                                        'tags' => ['درس_هشتم:_افتخار_بندگی'],
                                        'children' => [
                                            [
                                                'id' => '515',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '516',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '520',
                                        'name' => 'درس نهم: به سوی پاکی',
                                        'tags' => ['درس_نهم:_به_سوی_پاکی'],
                                        'children' => [
                                            [
                                                'id' => '518',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '519',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '523',
                                        'name' => 'درس دهم: ستون دین',
                                        'tags' => ['درس_دهم:_ستون_دین'],
                                        'children' => [
                                            [
                                                'id' => '521',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '522',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '526',
                                        'name' => 'درس یازدهم: نماز جماعت',
                                        'tags' => ['درس_یازدهم:_نماز_جماعت'],
                                        'children' => [
                                            [
                                                'id' => '524',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '525',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '529',
                                        'name' => 'درس دوازدهم: نشان عزّت',
                                        'tags' => ['درس_دوازدهم:_نشان_عزّت'],
                                        'children' => [
                                            [
                                                'id' => '527',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '528',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '532',
                                        'name' => 'درس سیزدهم: بر بال فرشتگان',
                                        'tags' => ['درس_سیزدهم:_بر_بال_فرشتگان'],

                                        'children' => [
                                            [
                                                'id' => '530',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '531',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '535',
                                        'name' => 'درس چهاردهم: کمال همنشین',
                                        'tags' => ['درس_چهاردهم:_کمال_همنشین'],
                                        'children' => [
                                            [
                                                'id' => '533',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '534',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '538',
                                        'name' => 'درس پانزدهم: مزدوران شیطان',
                                        'tags' => ['درس_پانزدهم:_مزدوران_شیطان'],

                                        'children' => [
                                            [
                                                'id' => '536',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '537',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '541',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '539',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '540',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],

                        ],
                    ],
                    [
                        'id' => '1079',
                        'name' => 'هشتم',
                        'tags' => ['هشتم'],
                        'children' => [
                            [
                                'id' => '587',
                                'name' => 'ریاضی',
                                'tags' => ['ریاضی'],
                                'children' => [
                                    [
                                        'id' => '548',
                                        'name' => 'فصل 1: عددهای صحیح و گویا',
                                        'tags' => ['فصل_1:_عددهای_صحیح_و_گویا'],

                                        'children' => [
                                            [
                                                'id' => '544',
                                                'name' => 'درس اول: یادآوری عددهای صحیح',
                                                'tags' => ['درس_اول:_یادآوری_عددهای_صحیح'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '545',
                                                'name' => 'درس دوم: معرفی عددهای گویا',
                                                'tags' => ['درس_دوم:_معرفی_عددهای_گویا'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '546',
                                                'name' => 'درس سوم: جمع و تفریق عددهای گویا',
                                                'tags' => ['درس_سوم:_جمع_و_تفریق_عددهای_گویا'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '547',
                                                'name' => 'درس چهارم: ضرب و تقسیم عددهای گویا',
                                                'tags' => ['درس_چهارم:_ضرب_و_تقسیم_عددهای_گویا'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '551',
                                        'name' => 'فصل 2: عددهای اول',
                                        'tags' => ['فصل_2:_عددهای_اول'],
                                        'children' => [
                                            [
                                                'id' => '549',
                                                'name' => 'درس اول: یادآوری عددهای اول',
                                                'tags' => ['درس_اول:_یادآوری_عددهای_اول'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '550',
                                                'name' => 'درس دوم: تعیین عددهای اول',
                                                'tags' => ['درس_دوم:_تعیین_عددهای_اول'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '557',
                                        'name' => 'فصل 3: چندضلعی‌ها',
                                        'tags' => ['فصل_3:_چندضلعی‌ها'],
                                        'children' => [
                                            [
                                                'id' => '552',
                                                'name' => 'درس اول: چندضلعی‌ها و تقارن',
                                                'tags' => ['درس_اول:_چندضلعی‌ها_و_تقارن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '553',
                                                'name' => 'درس دوم: توازی و تعامد',
                                                'tags' => ['درس_دوم:_توازی_و_تعامد'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '554',
                                                'name' => 'درس سوم: چهار ضلعی‌ها',
                                                'tags' => ['درس_سوم:_چهار_ضلعی‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '555',
                                                'name' => 'درس چهارم: زاویه‌های داخلی',
                                                'tags' => ['درس_چهارم:_زاویه‌های_داخلی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '556',
                                                'name' => 'درس پنجم: زاویه‌های خارجی',
                                                'tags' => ['درس_پنجم:_زاویه‌های_خارجی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '562',
                                        'name' => 'فصل 4: جبر و معادله',
                                        'tags' => ['فصل_4:_جبر_و_معادله'],
                                        'children' => [
                                            [
                                                'id' => '558',
                                                'name' => 'درس اول: ساده‌کردن عبارت‌های جبری',
                                                'tags' => ['درس_اول:_ساده‌کردن_عبارت‌های_جبری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '559',
                                                'name' => 'درس دوم: پیدا کردن مقدار یک عبارت جبری',
                                                'tags' => ['درس_دوم:_پیدا_کردن_مقدار_یک_عبارت_جبری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '560',
                                                'name' => 'درس سوم: تجزیۀ عبارت‌های جبری',
                                                'tags' => ['درس_سوم:_تجزیۀ_عبارت‌های_جبری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '561',
                                                'name' => 'درس چهارم: معادله',
                                                'tags' => ['درس_چهارم:_معادله'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '566',
                                        'name' => 'فصل 5: بردار و مختصات',
                                        'tags' => ['فصل_5:_بردار_و_مختصات'],
                                        'children' => [
                                            [
                                                'id' => '563',
                                                'name' => 'درس اول: جمع بردارها',
                                                'tags' => ['درس_اول:_جمع_بردارها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '564',
                                                'name' => 'درس دوم: ضرب عدد در بردار',
                                                'tags' => ['درس_دوم:_ضرب_عدد_در_بردار'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '565',
                                                'name' => 'درس سوم: بردارهای واحد مختصات',
                                                'tags' => ['درس_سوم:_بردارهای_واحد_مختصات'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '571',
                                        'name' => 'فصل 6: مثلث',
                                        'tags' => ['فصل_6:_مثلث'],
                                        'children' => [
                                            [
                                                'id' => '567',
                                                'name' => 'درس اول: رابطۀ فیثاغورس',
                                                'tags' => ['درس_اول:_رابطۀ_فیثاغورس'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '568',
                                                'name' => 'درس دوم: شکل‌های همنهشت',
                                                'tags' => ['درس_دوم:_شکل‌های_همنهشت'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '569',
                                                'name' => 'درس سوم: مثلث‌های همنهشت',
                                                'tags' => ['درس_سوم:_مثلث‌های_همنهشت'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '570',
                                                'name' => 'درس چهارم: همنهشتی مثلث‌های قائم‌الزاویه',
                                                'tags' => ['درس_چهارم:_همنهشتی_مثلث‌های_قائم‌الزاویه'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '577',
                                        'name' => 'فصل 7: توان و جذر',
                                        'tags' => ['فصل_7:_توان_و_جذر'],
                                        'children' => [
                                            [
                                                'id' => '572',
                                                'name' => 'درس اول: توان',
                                                'tags' => ['درس_اول:_توان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '573',
                                                'name' => 'درس دوم: تقسیم اعداد توان‌دار',
                                                'tags' => ['درس_دوم:_تقسیم_اعداد_توان‌دار'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '574',
                                                'name' => 'درس سوم: جذر تقریبی',
                                                'tags' => ['درس_سوم:_جذر_تقریبی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '575',
                                                'name' => 'درس چهارم: نمایش اعداد رادیکالی روی محور اعداد',
                                                'tags' => ['درس_چهارم:_نمایش_اعداد_رادیکالی_روی_محور_اعداد'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '576',
                                                'name' => 'درس پنجم: خواص ضرب و تقسیم رادیکال‌ها',
                                                'tags' => ['درس_پنجم:_خواص_ضرب_و_تقسیم_رادیکال‌ها'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '582',
                                        'name' => 'فصل 8: آمار و احتمال',
                                        'tags' => ['فصل_8:_آمار_و_احتمال'],
                                        'children' => [
                                            [
                                                'id' => '578',
                                                'name' => 'درس اول: دسته‌بندی داده‌ها',
                                                'tags' => ['درس_اول:_دسته‌بندی_داده‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '579',
                                                'name' => 'درس دوم: میانگین داده‌ها',
                                                'tags' => ['درس_دوم:_میانگین_داده‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '580',
                                                'name' => 'درس سوم: احتمال یا اندازه‌گیری شانس',
                                                'tags' => ['درس_سوم:_احتمال_یا_اندازه‌گیری_شانس'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '581',
                                                'name' => 'درس چهارم: بررسی حالت‌های ممکن',
                                                'tags' => ['درس_چهارم:_بررسی_حالت‌های_ممکن'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '586',
                                        'name' => 'فصل 9: دایره',
                                        'tags' => ['فصل_9:_دایره'],
                                        'children' => [
                                            [
                                                'id' => '583',
                                                'name' => 'درس اول: خط و دایره',
                                                'tags' => ['درس_اول:_خط_و_دایره'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '584',
                                                'name' => 'درس دوم: زاویه‌های مرکزی',
                                                'tags' => ['درس_دوم:_زاویه‌های_مرکزی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '585',
                                                'name' => 'درس سوم: زاویه‌های محاطی',
                                                'tags' => ['درس_سوم:_زاویه‌های_محاطی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '643',
                                'name' => 'زبان انگلیسی',
                                'tags' => ['زبان_انگلیسی'],
                                'children' => [
                                    [
                                        'id' => '594',
                                        'name' => 'Lesson 1: My Nationality',
                                        'tags' => ['Lesson_1:_My_Nationality'],
                                        'children' => [
                                            [
                                                'id' => '588',
                                                'name' => 'Spelling and Pronunciation',
                                                'tags' => ['Spelling_and_Pronunciation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '589',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '590',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '591',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '592',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '593',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '601',
                                        'name' => 'Lesson 2: My Week',
                                        'tags' => ['Lesson_2:_My_Week'],
                                        'children' => [
                                            [
                                                'id' => '595',
                                                'name' => 'Spelling and Pronunciation',
                                                'tags' => ['Spelling_and_Pronunciation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '596',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '597',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '598',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '599',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '600',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '608',
                                        'name' => 'Lesson 3: My Abilities',
                                        'tags' => ['Lesson_3:_My_Abilities'],
                                        'children' => [
                                            [
                                                'id' => '602',
                                                'name' => 'Spelling and Pronunciation',
                                                'tags' => ['Spelling_and_Pronunciation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '603',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '604',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '605',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '606',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '607',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '615',
                                        'name' => 'Lesson 4: My Health',
                                        'tags' => ['Lesson_4:_My_Health'],
                                        'children' => [
                                            [
                                                'id' => '609',
                                                'name' => 'Spelling and Pronunciation',
                                                'tags' => ['Spelling_and_Pronunciation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '610',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '611',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '612',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '613',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '614',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '622',
                                        'name' => 'Lesson 5: My City',
                                        'tags' => ['Lesson_5:_My_City'],
                                        'children' => [
                                            [
                                                'id' => '616',
                                                'name' => 'Spelling and Pronunciation',
                                                'tags' => ['Spelling_and_Pronunciation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '617',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '618',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '619',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '620',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '621',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '629',
                                        'name' => 'Lesson 6: My Village',
                                        'tags' => ['Lesson_6:_My_Village'],
                                        'children' => [
                                            [
                                                'id' => '623',
                                                'name' => 'Spelling and Pronunciation',
                                                'tags' => ['Spelling_and_Pronunciation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '624',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '625',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '626',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '627',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '628',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '636',
                                        'name' => 'Lesson 7: My Hobbies',
                                        'tags' => ['Lesson_7:_My_Hobbies'],
                                        'children' => [
                                            [
                                                'id' => '630',
                                                'name' => 'Spelling and Pronunciation',
                                                'tags' => ['Spelling_and_Pronunciation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '631',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '632',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '633',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '634',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '635',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '642',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '637',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '638',
                                                'name' => 'Cloze ',
                                                'tags' => ['Cloze_'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '639',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '640',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '641',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '688',
                                'name' => 'عربی',
                                'tags' => ['عربی'],
                                'children' => [
                                    [
                                        'id' => '647',
                                        'name' => 'الدرس الأول: مراجعة دروس الصف السابع',
                                        'tags' => ['الدرس_الأول:_مراجعة_دروس_الصف_السابع'],

                                        'children' => [
                                            [
                                                'id' => '644',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '645',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '646',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '651',
                                        'name' => 'الدرس الثانی: اهمیة اللغة العربیة',
                                        'tags' => ['الدرس_الثانی:_اهمیة_اللغة_العربیة'],

                                        'children' => [
                                            [
                                                'id' => '648',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '649',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '650',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '655',
                                        'name' => 'الدرس الثالث: مهنتک فی المستقبل',
                                        'tags' => ['الدرس_الثالث:_مهنتک_فی_المستقبل'],

                                        'children' => [
                                            [
                                                'id' => '652',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '653',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '654',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '659',
                                        'name' => 'الدرس الرابع: التجربة الجدیدة',
                                        'tags' => ['الدرس_الرابع:_التجربة_الجدیدة'],

                                        'children' => [
                                            [
                                                'id' => '656',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '657',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '658',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '663',
                                        'name' => 'الدرس الخامس: الصداقة',
                                        'tags' => ['الدرس_الخامس:_الصداقة'],
                                        'children' => [
                                            [
                                                'id' => '660',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '661',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '662',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '667',
                                        'name' => 'الدرس السادس: فی السفر',
                                        'tags' => ['الدرس_السادس:_فی_السفر'],
                                        'children' => [
                                            [
                                                'id' => '664',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '665',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '666',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '671',
                                        'name' => 'الدرس السابع: ﴿... ارض الله واسعة﴾',
                                        'tags' => ['الدرس_السابع:_﴿..._ارض_الله_واسعة﴾'],

                                        'children' => [
                                            [
                                                'id' => '668',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '669',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '670',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '675',
                                        'name' => 'الدرس الثامن: الاعتماد علی النفس',
                                        'tags' => ['الدرس_الثامن:_الاعتماد_علی_النفس'],

                                        'children' => [
                                            [
                                                'id' => '672',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '673',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '674',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '679',
                                        'name' => 'الدرس التاسع: السفرة العلمیة',
                                        'tags' => ['الدرس_التاسع:_السفرة_العلمیة'],

                                        'children' => [
                                            [
                                                'id' => '676',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '677',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '678',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '683',
                                        'name' => 'الدرس العاشر: الحکم',
                                        'tags' => ['الدرس_العاشر:_الحکم'],
                                        'children' => [
                                            [
                                                'id' => '680',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '681',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '682',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '687',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '684',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '685',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '686',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '794',
                                'name' => 'علوم',
                                'tags' => ['علوم'],
                                'children' => [
                                    [
                                        'id' => '695',
                                        'name' => 'فصل اول: مخلوط و جداسازی مواد',
                                        'tags' => ['فصل_اول:_مخلوط_و_جداسازی_مواد'],

                                        'children' => [
                                            [
                                                'id' => '689',
                                                'name' => 'مواد خالص و مخلوط',
                                                'tags' => ['مواد_خالص_و_مخلوط'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '690',
                                                'name' => 'انواع مخلوط‌ها (همگن و ناهمگن)',
                                                'tags' => ['انواع_مخلوط‌ها_(همگن_و_ناهمگن)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '691',
                                                'name' => 'اجزای تشکیل‌دهنده و حالت فیزیکی محلول‌ها',
                                                'tags' => ['اجزای_تشکیل‌دهنده_و_حالت_فیزیکی_محلول‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '692',
                                                'name' => 'انحلال‌پذیری و عوامل مؤثر بر آن',
                                                'tags' => ['انحلال‌پذیری_و_عوامل_مؤثر_بر_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '693',
                                                'name' => 'اسیدها و بازها',
                                                'tags' => ['اسیدها_و_بازها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '694',
                                                'name' => 'جداسازی مخلوط‌ها',
                                                'tags' => ['جداسازی_مخلوط‌ها'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '700',
                                        'name' => 'فصل دوم: تغییرهای شیمیایی در خدمت زندگی',
                                        'tags' => ['فصل_دوم:_تغییرهای_شیمیایی_در_خدمت_زندگی'],

                                        'children' => [
                                            [
                                                'id' => '696',
                                                'name' => 'تغییرهای فیزیکی و شیمیایی',
                                                'tags' => ['تغییرهای_فیزیکی_و_شیمیایی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '697',
                                                'name' => 'سوختن و فرآورده‌های آن',
                                                'tags' => ['سوختن_و_فرآورده‌های_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '698',
                                                'name' => 'تغییر شیمیایی در بدن جانداران و عوامل مؤثر بر سرعت تغییرها',
                                                'tags' => ['تغییر_شیمیایی_در_بدن_جانداران_و_عوامل_مؤثر_بر_سرعت_تغییرها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '699',
                                                'name' => 'استفاده از انرژی شیمیایی مواد - پیل‌های شیمیایی',
                                                'tags' => ['استفاده_از_انرژی_شیمیایی_مواد_-_پیل‌های_شیمیایی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '706',
                                        'name' => 'فصل سوم: از درون اتم چه خبر',
                                        'tags' => ['فصل_سوم:_از_درون_اتم_چه_خبر'],

                                        'children' => [
                                            [
                                                'id' => '701',
                                                'name' => 'ذره‌های سازنده اتم',
                                                'tags' => ['ذره‌های_سازنده_اتم'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '702',
                                                'name' => 'عنصرها و نشانه شیمیایی آن‌ها',
                                                'tags' => ['عنصرها_و_نشانه_شیمیایی_آن‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '703',
                                                'name' => 'مدلی برای ساختار اتم',
                                                'tags' => ['مدلی_برای_ساختار_اتم'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '704',
                                                'name' => 'ایزوتوپ‌ها',
                                                'tags' => ['ایزوتوپ‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '705',
                                                'name' => 'یون‌ها',
                                                'tags' => ['یون‌ها'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '713',
                                        'name' => 'فصل چهارم: تنظیم عصبی',
                                        'tags' => ['فصل_چهارم:_تنظیم_عصبی'],
                                        'children' => [
                                            [
                                                'id' => '707',
                                                'name' => 'دستگاه عصبی',
                                                'tags' => ['دستگاه_عصبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '708',
                                                'name' => 'فعالیت‌های ارادی و غیرارادی',
                                                'tags' => ['فعالیت‌های_ارادی_و_غیرارادی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '709',
                                                'name' => 'مراکز عصبی (مغز و نخاع)',
                                                'tags' => ['مراکز_عصبی_(مغز_و_نخاع)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '710',
                                                'name' => 'اعصاب محیطی (حسی و حرکتی)',
                                                'tags' => ['اعصاب_محیطی_(حسی_و_حرکتی)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '711',
                                                'name' => 'سلول‌های بافت عصبی',
                                                'tags' => ['سلول‌های_بافت_عصبی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '712',
                                                'name' => 'پیام عصبی',
                                                'tags' => ['پیام_عصبی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '723',
                                        'name' => 'فصل پنجم: حس و حرکت',
                                        'tags' => ['فصل_پنجم:_حس_و_حرکت'],
                                        'children' => [
                                            [
                                                'id' => '719',
                                                'name' => 'اندام‌های حسی',
                                                'tags' => ['اندام‌های_حسی'],
                                                'children' => [
                                                    [
                                                        'id' => '714',
                                                        'name' => 'چشم',
                                                        'tags' => ['چشم'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '715',
                                                        'name' => 'گوش',
                                                        'tags' => ['گوش'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '716',
                                                        'name' => 'بینی',
                                                        'tags' => ['بینی'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '717',
                                                        'name' => 'زبان',
                                                        'tags' => ['زبان'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '718',
                                                        'name' => 'پوست',
                                                        'tags' => ['پوست'],
                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],
                                            [
                                                'id' => '722',
                                                'name' => 'دستگاه حرکتی',
                                                'tags' => ['دستگاه_حرکتی'],
                                                'children' => [
                                                    [
                                                        'id' => '720',
                                                        'name' => 'اسکلت',
                                                        'tags' => ['اسکلت'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '721',
                                                        'name' => 'ماهیچه‌ها',
                                                        'tags' => ['ماهیچه‌ها'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '732',
                                        'name' => 'فصل ششم: تنظیم هورمونی',
                                        'tags' => ['فصل_ششم:_تنظیم_هورمونی'],
                                        'children' => [
                                            [
                                                'id' => '724',
                                                'name' => 'دستگاه هورمونی و اعمال هورمون‌ها',
                                                'tags' => ['دستگاه_هورمونی_و_اعمال_هورمون‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '725',
                                                'name' => 'هیپوفیز',
                                                'tags' => ['هیپوفیز'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '726',
                                                'name' => 'تیروئید',
                                                'tags' => ['تیروئید'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '727',
                                                'name' => 'پانکراس',
                                                'tags' => ['پانکراس'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '728',
                                                'name' => 'فوق کلیوی',
                                                'tags' => ['فوق_کلیوی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '729',
                                                'name' => 'پاراتیروئید',
                                                'tags' => ['پاراتیروئید'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '730',
                                                'name' => 'غدد جنسی',
                                                'tags' => ['غدد_جنسی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '731',
                                                'name' => 'تنظیم ترشح هورمون‌ها',
                                                'tags' => ['تنظیم_ترشح_هورمون‌ها'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '739',
                                        'name' => 'فصل هفتم: الفبای زیست‌فناوری',
                                        'tags' => ['فصل_هفتم:_الفبای_زیست‌فناوری'],

                                        'children' => [
                                            [
                                                'id' => '733',
                                                'name' => 'صفات ارثی',
                                                'tags' => ['صفات_ارثی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '734',
                                                'name' => 'نگاهی دقیق به هستۀ سلول',
                                                'tags' => ['نگاهی_دقیق_به_هستۀ_سلول'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '735',
                                                'name' => 'صفات محیطی',
                                                'tags' => ['صفات_محیطی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '736',
                                                'name' => 'ایجاد صفات جدید در جانداران',
                                                'tags' => ['ایجاد_صفات_جدید_در_جانداران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '737',
                                                'name' => 'تقسیم میتوز',
                                                'tags' => ['تقسیم_میتوز'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '738',
                                                'name' => 'تقسیم مشکل‌ساز (سرطان)',
                                                'tags' => ['تقسیم_مشکل‌ساز_(سرطان)'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '746',
                                        'name' => 'فصل هشتم: تولید‌مثل در جانداران',
                                        'tags' => ['فصل_هشتم:_تولید‌مثل_در_جانداران'],

                                        'children' => [
                                            [
                                                'id' => '740',
                                                'name' => 'تولید مثل غیرجنسی',
                                                'tags' => ['تولید_مثل_غیرجنسی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '741',
                                                'name' => 'تولید مثل‌جنسی',
                                                'tags' => ['تولید_مثل‌جنسی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '742',
                                                'name' => 'تقسیم میوز',
                                                'tags' => ['تقسیم_میوز'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '743',
                                                'name' => 'تولید‌مثل جنسی در جانوران',
                                                'tags' => ['تولید‌مثل_جنسی_در_جانوران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '744',
                                                'name' => 'تولید مثل در انسان',
                                                'tags' => ['تولید_مثل_در_انسان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '745',
                                                'name' => 'تولید مثل جنسی در گیاهان گلدار',
                                                'tags' => ['تولید_مثل_جنسی_در_گیاهان_گلدار'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '755',
                                        'name' => 'فصل نهم: الکتریسیته',
                                        'tags' => ['فصل_نهم:_الکتریسیته'],
                                        'children' => [
                                            [
                                                'id' => '747',
                                                'name' => 'بارهای الکتریکی',
                                                'tags' => ['بارهای_الکتریکی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '748',
                                                'name' => 'رسانا و نارسانا',
                                                'tags' => ['رسانا_و_نارسانا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '749',
                                                'name' => 'روش‌های باردارکردن اجسام',
                                                'tags' => ['روش‌های_باردارکردن_اجسام'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '750',
                                                'name' => 'آذرخش و تخلیۀ بار الکتریکی',
                                                'tags' => ['آذرخش_و_تخلیۀ_بار_الکتریکی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '751',
                                                'name' => 'برق‌نما',
                                                'tags' => ['برق‌نما'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '752',
                                                'name' => 'اختلاف‌پتانسیل الکتریکی',
                                                'tags' => ['اختلاف‌پتانسیل_الکتریکی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '753',
                                                'name' => 'مدار الکتریکی و جریان الکتریکی',
                                                'tags' => ['مدار_الکتریکی_و_جریان_الکتریکی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '754',
                                                'name' => 'مقاومت الکتریکی',
                                                'tags' => ['مقاومت_الکتریکی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '760',
                                        'name' => 'فصل دهم: مغناطیس',
                                        'tags' => ['فصل_دهم:_مغناطیس'],
                                        'children' => [
                                            [
                                                'id' => '756',
                                                'name' => 'قطب‌های آهنربا',
                                                'tags' => ['قطب‌های_آهنربا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '757',
                                                'name' => 'روش‌های ساخت آهنربا',
                                                'tags' => ['روش‌های_ساخت_آهنربا'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '758',
                                                'name' => 'آهنربای الکتریکی - موتور الکتریکی',
                                                'tags' => ['آهنربای_الکتریکی_-_موتور_الکتریکی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '759',
                                                'name' => 'تولید برق',
                                                'tags' => ['تولید_برق'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '768',
                                        'name' => 'فصل یازدهم: کانی‌ها',
                                        'tags' => ['فصل_یازدهم:_کانی‌ها'],
                                        'children' => [
                                            [
                                                'id' => '761',
                                                'name' => 'کانی چیست؟',
                                                'tags' => ['کانی_چیست؟'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '762',
                                                'name' => 'کاربرد کانی‌ها',
                                                'tags' => ['کاربرد_کانی‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '763',
                                                'name' => 'تشکیل کانی‌ها',
                                                'tags' => ['تشکیل_کانی‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '764',
                                                'name' => 'شناسایی کانی‌ها',
                                                'tags' => ['شناسایی_کانی‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '765',
                                                'name' => 'کانی‌های نامهربان',
                                                'tags' => ['کانی‌های_نامهربان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '766',
                                                'name' => 'نام‌گذاری کانی‌ها و کانی‌های ملی',
                                                'tags' => ['نام‌گذاری_کانی‌ها_و_کانی‌های_ملی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '767',
                                                'name' => 'طبقه‌بندی کانی‌ها',
                                                'tags' => ['طبقه‌بندی_کانی‌ها'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '773',
                                        'name' => 'فصل دوازدهم: سنگ‌ها',
                                        'tags' => ['فصل_دوازدهم:_سنگ‌ها'],
                                        'children' => [
                                            [
                                                'id' => '769',
                                                'name' => 'سنگ‌ها، منابع ارزشمند',
                                                'tags' => ['سنگ‌ها،_منابع_ارزشمند'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '770',
                                                'name' => 'سنگ‌های آذرین',
                                                'tags' => ['سنگ‌های_آذرین'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '771',
                                                'name' => 'سنگ‌های رسوبی',
                                                'tags' => ['سنگ‌های_رسوبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '772',
                                                'name' => 'سنگ‌های دگرگونی',
                                                'tags' => ['سنگ‌های_دگرگونی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '779',
                                        'name' => 'فصل سیزدهم: سنگ‌ها چگونه تغییر می‌کنند؟',
                                        'tags' => ['فصل_سیزدهم:_سنگ‌ها_چگونه_تغییر_می‌کنند؟'],

                                        'children' => [
                                            [
                                                'id' => '776',
                                                'name' => 'هوازدگی',
                                                'tags' => ['هوازدگی'],
                                                'children' => [
                                                    [
                                                        'id' => '774',
                                                        'name' => 'فیزیکی',
                                                        'tags' => ['فیزیکی'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '775',
                                                        'name' => 'شیمیایی',
                                                        'tags' => ['شیمیایی'],
                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],
                                            [
                                                'id' => '777',
                                                'name' => 'فرسایش',
                                                'tags' => ['فرسایش'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '778',
                                                'name' => 'چرخۀ سنگ',
                                                'tags' => ['چرخۀ_سنگ'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '787',
                                        'name' => 'فصل چهاردهم: نور و ویژگی‌های آن',
                                        'tags' => ['فصل_چهاردهم:_نور_و_ویژگی‌های_آن'],

                                        'children' => [
                                            [
                                                'id' => '780',
                                                'name' => 'چشمه‌های نور - چگونگی انتشار نور',
                                                'tags' => ['چشمه‌های_نور_-_چگونگی_انتشار_نور'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '781',
                                                'name' => 'سایه و نیم‌سایه',
                                                'tags' => ['سایه_و_نیم‌سایه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '782',
                                                'name' => 'بازتاب نور',
                                                'tags' => ['بازتاب_نور'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '783',
                                                'name' => 'تصویر در آینۀ تخت',
                                                'tags' => ['تصویر_در_آینۀ_تخت'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '786',
                                                'name' => 'آینه‌های کروی',
                                                'tags' => ['آینه‌های_کروی'],
                                                'children' => [
                                                    [
                                                        'id' => '784',
                                                        'name' => 'آینه‌های کاو',
                                                        'tags' => ['آینه‌های_کاو'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '785',
                                                        'name' => 'آینه‌های کوژ',
                                                        'tags' => ['آینه‌های_کوژ'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '793',
                                        'name' => 'فصل پانزدهم: شکست نور',
                                        'tags' => ['فصل_پانزدهم:_شکست_نور'],
                                        'children' => [
                                            [
                                                'id' => '788',
                                                'name' => 'شکست نور',
                                                'tags' => ['شکست_نور'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '789',
                                                'name' => 'منشور',
                                                'tags' => ['منشور'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '792',
                                                'name' => 'عدسی‌ها',
                                                'tags' => ['عدسی‌ها'],
                                                'children' => [
                                                    [
                                                        'id' => '790',
                                                        'name' => 'عدسی‌های همگرا',
                                                        'tags' => ['عدسی‌های_همگرا'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '791',
                                                        'name' => 'عدسی‌های واگرا',
                                                        'tags' => ['عدسی‌های_واگرا'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '924',
                                'name' => 'فارسی',
                                'tags' => ['فارسی'],
                                'children' => [
                                    [
                                        'id' => '801',
                                        'name' => 'درس اول: پیش از این‌ها',
                                        'tags' => ['درس_اول:_پیش_از_این‌ها'],
                                        'children' => [
                                            [
                                                'id' => '795',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '796',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '797',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '798',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '799',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '800',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '808',
                                        'name' => 'درس دوم: خوب، جهان را ببین!، صورتگر ماهر',
                                        'tags' => ['درس_دوم:_خوب،_جهان_را_ببین!،_صورتگر_ماهر'],

                                        'children' => [
                                            [
                                                'id' => '802',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '803',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '804',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '805',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '806',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '807',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '815',
                                        'name' => 'درس سوم: ارمغان ایران',
                                        'tags' => ['درس_سوم:_ارمغان_ایران'],
                                        'children' => [
                                            [
                                                'id' => '809',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '810',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '811',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '812',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '813',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '814',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '823',
                                        'name' => 'درس چهارم: سفر شکفتن',
                                        'tags' => ['درس_چهارم:_سفر_شکفتن'],
                                        'children' => [
                                            [
                                                'id' => '816',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '817',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '818',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '819',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '820',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '821',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '822',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '830',
                                        'name' => 'درس ششم: راه نیک‌بختی',
                                        'tags' => ['درس_ششم:_راه_نیک‌بختی'],
                                        'children' => [
                                            [
                                                'id' => '824',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '825',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '826',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '827',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '828',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '829',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '837',
                                        'name' => 'درس هفتم: آداب نیکان',
                                        'tags' => ['درس_هفتم:_آداب_نیکان'],
                                        'children' => [
                                            [
                                                'id' => '831',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '832',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '833',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '834',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '835',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '836',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '844',
                                        'name' => 'درس هشتم: آزادگی',
                                        'tags' => ['درس_هشتم:_آزادگی'],
                                        'children' => [
                                            [
                                                'id' => '838',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '839',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '840',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '841',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '842',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '843',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '851',
                                        'name' => 'درس نهم: نوجوان باهوش، آشپز زادۀ وزیر، گریۀ امیر',
                                        'tags' => ['درس_نهم:_نوجوان_باهوش،_آشپز_زادۀ_وزیر،_گریۀ_امیر'],

                                        'children' => [
                                            [
                                                'id' => '845',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '846',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '847',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '848',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '849',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '850',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '858',
                                        'name' => 'درس دهم: قلم سحرآمیز، دو نامه',
                                        'tags' => ['درس_دهم:_قلم_سحرآمیز،_دو_نامه'],

                                        'children' => [
                                            [
                                                'id' => '852',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '853',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '854',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '855',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '856',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '857',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '866',
                                        'name' => 'درس یازدهم: پرچم‌داران',
                                        'tags' => ['درس_یازدهم:_پرچم‌داران'],
                                        'children' => [
                                            [
                                                'id' => '859',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '860',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '861',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '862',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '863',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '864',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '865',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '873',
                                        'name' => 'درس دوازدهم: شیر حق',
                                        'tags' => ['درس_دوازدهم:_شیر_حق'],
                                        'children' => [
                                            [
                                                'id' => '867',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '868',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '869',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '870',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '871',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '872',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '880',
                                        'name' => 'درس سیزدهم: ادبیات انقلاب',
                                        'tags' => ['درس_سیزدهم:_ادبیات_انقلاب'],

                                        'children' => [
                                            [
                                                'id' => '874',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '875',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '876',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '877',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '878',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '879',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '887',
                                        'name' => 'درس چهاردهم: یاد حسین',
                                        'tags' => ['درس_چهاردهم:_یاد_حسین'],
                                        'children' => [
                                            [
                                                'id' => '881',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '882',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '883',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '884',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '885',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '886',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '894',
                                        'name' => 'درس شانزدهم: پرندۀ آزادی، کودکان سنگ',
                                        'tags' => ['درس_شانزدهم:_پرندۀ_آزادی،_کودکان_سنگ'],

                                        'children' => [
                                            [
                                                'id' => '888',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '889',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '890',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '891',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '892',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '893',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '901',
                                        'name' => 'درس هفدهم: راه خوشبختی',
                                        'tags' => ['درس_هفدهم:_راه_خوشبختی'],
                                        'children' => [
                                            [
                                                'id' => '895',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '896',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '897',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '898',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '899',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '900',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '908',
                                        'name' => 'ستایش: به نام خدایی که جان آفرید',
                                        'tags' => ['ستایش:_به_نام_خدایی_که_جان_آفرید'],

                                        'children' => [
                                            [
                                                'id' => '902',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '903',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '904',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '905',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '906',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '907',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '915',
                                        'name' => 'نیایش',
                                        'tags' => ['نیایش'],
                                        'children' => [
                                            [
                                                'id' => '909',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '910',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '911',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '912',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '913',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '914',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '923',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '916',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '917',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '918',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '919',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '920',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '921',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '922',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '1032',
                                'name' => 'مطالعات اجتماعی',
                                'tags' => ['مطالعات_اجتماعی'],
                                'children' => [
                                    [
                                        'id' => '927',
                                        'name' => 'درس 1: تعاون (1)',
                                        'tags' => ['درس_1:_تعاون_(1)'],
                                        'children' => [
                                            [
                                                'id' => '925',
                                                'name' => 'تعاون و شکل‌های مختلف آن',
                                                'tags' => ['تعاون_و_شکل‌های_مختلف_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '926',
                                                'name' => 'تعاون در خانه، مدرسه و محله',
                                                'tags' => ['تعاون_در_خانه،_مدرسه_و_محله'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '931',
                                        'name' => 'درس 2: تعاون (2)',
                                        'tags' => ['درس_2:_تعاون_(2)'],
                                        'children' => [
                                            [
                                                'id' => '928',
                                                'name' => 'انفاق',
                                                'tags' => ['انفاق'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '929',
                                                'name' => 'وقف',
                                                'tags' => ['وقف'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '930',
                                                'name' => 'شرکت‌های تعاونی',
                                                'tags' => ['شرکت‌های_تعاونی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '936',
                                        'name' => 'درس 3: ساختار و تشکیلات دولت',
                                        'tags' => ['درس_3:_ساختار_و_تشکیلات_دولت'],

                                        'children' => [
                                            [
                                                'id' => '932',
                                                'name' => 'قوۀ مجریه',
                                                'tags' => ['قوۀ_مجریه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '933',
                                                'name' => 'انتخاب رئیس‌جمهور',
                                                'tags' => ['انتخاب_رئیس‌جمهور'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '934',
                                                'name' => 'تنفیذ و تحلیف',
                                                'tags' => ['تنفیذ_و_تحلیف'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '935',
                                                'name' => 'کابینه',
                                                'tags' => ['کابینه'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '941',
                                        'name' => 'درس 4: وظایف دولت',
                                        'tags' => ['درس_4:_وظایف_دولت'],
                                        'children' => [
                                            [
                                                'id' => '937',
                                                'name' => 'دولت و شهروندان',
                                                'tags' => ['دولت_و_شهروندان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '938',
                                                'name' => 'مهم‌ترین وظایف دولت و رئیس‌جمهور',
                                                'tags' => ['مهم‌ترین_وظایف_دولت_و_رئیس‌جمهور'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '939',
                                                'name' => 'دولت و مجلس',
                                                'tags' => ['دولت_و_مجلس'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '940',
                                                'name' => 'درآمد و هزینه‌های دولت',
                                                'tags' => ['درآمد_و_هزینه‌های_دولت'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '944',
                                        'name' => 'درس 5: آسیب‌های اجتماعی و پیشگیری از آن‌ها',
                                        'tags' => ['درس_5:_آسیب‌های_اجتماعی_و_پیشگیری_از_آن‌ها'],

                                        'children' => [
                                            [
                                                'id' => '942',
                                                'name' => 'دورۀ نوجوانی',
                                                'tags' => ['دورۀ_نوجوانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '943',
                                                'name' => 'آسیب‌های اجتماعی',
                                                'tags' => ['آسیب‌های_اجتماعی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '948',
                                        'name' => 'درس 6: قوۀ قضائیه',
                                        'tags' => ['درس_6:_قوۀ_قضائیه'],
                                        'children' => [
                                            [
                                                'id' => '945',
                                                'name' => 'افرادی که از نوجوان در برابر آسیب‌ها و تهدیدات محافظت می‌کنند',
                                                'tags' => ['افرادی_که_از_نوجوان_در_برابر_آسیب‌ها_و_تهدیدات_محافظت_می‌کنند'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '946',
                                                'name' => 'قوۀ قضائیه',
                                                'tags' => ['قوۀ_قضائیه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '947',
                                                'name' => 'رسیدگی به شکایت‌های مردم و حل اختلاف',
                                                'tags' => ['رسیدگی_به_شکایت‌های_مردم_و_حل_اختلاف'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '953',
                                        'name' => 'درس 7: ارتباط و رسانه',
                                        'tags' => ['درس_7:_ارتباط_و_رسانه'],
                                        'children' => [
                                            [
                                                'id' => '949',
                                                'name' => 'نیاز به ارتباط',
                                                'tags' => ['نیاز_به_ارتباط'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '950',
                                                'name' => 'عناصر ارتباط',
                                                'tags' => ['عناصر_ارتباط'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '951',
                                                'name' => 'رسانه',
                                                'tags' => ['رسانه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '952',
                                                'name' => 'وزارت ارتباطات و فناوری اطلاعات',
                                                'tags' => ['وزارت_ارتباطات_و_فناوری_اطلاعات'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '956',
                                        'name' => 'درس 8: رسانه‌ها در زندگی ما',
                                        'tags' => ['درس_8:_رسانه‌ها_در_زندگی_ما'],

                                        'children' => [
                                            [
                                                'id' => '954',
                                                'name' => 'کاربرد‌های رسانه‌ها در زندگی ما',
                                                'tags' => ['کاربرد‌های_رسانه‌ها_در_زندگی_ما'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '955',
                                                'name' => 'تأثیر وسایل ارتباط جمعی بر فرهنگ عمومی',
                                                'tags' => ['تأثیر_وسایل_ارتباط_جمعی_بر_فرهنگ_عمومی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '960',
                                        'name' => 'درس 9: ظهور اسلام در شبه‌جزیرۀ عربستان',
                                        'tags' => ['درس_9:_ظهور_اسلام_در_شبه‌جزیرۀ_عربستان'],

                                        'children' => [
                                            [
                                                'id' => '957',
                                                'name' => 'محیط پیدایش اسلام',
                                                'tags' => ['محیط_پیدایش_اسلام'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '958',
                                                'name' => 'طلوع آفتاب اسلام در مکه',
                                                'tags' => ['طلوع_آفتاب_اسلام_در_مکه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '959',
                                                'name' => 'تشکیل امت و حکومت اسلامی به رهبری پیامبر (ص) در مدینه',
                                                'tags' => ['تشکیل_امت_و_حکومت_اسلامی_به_رهبری_پیامبر_(ص)_در_مدینه'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '965',
                                        'name' => 'درس 10: از رحلت پیامبر (ص) تا قیام کربلا (نینوا)',
                                        'tags' => ['درس_10:_از_رحلت_پیامبر_(ص)_تا_قیام_کربلا_(نینوا)'],

                                        'children' => [
                                            [
                                                'id' => '961',
                                                'name' => 'وفات پیامبر (ص) و ماجرای سقیفه و جانشینی پیامبر (ص)',
                                                'tags' => ['وفات_پیامبر_(ص)_و_ماجرای_سقیفه_و_جانشینی_پیامبر_(ص)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '962',
                                                'name' => 'حکومت امام علی (ع)',
                                                'tags' => ['حکومت_امام_علی_(ع)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '963',
                                                'name' => 'صلح امام حسن (ع) با معاویه و روی کار آمدن امویان',
                                                'tags' => ['صلح_امام_حسن_(ع)_با_معاویه_و_روی_کار_آمدن_امویان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '964',
                                                'name' => 'قیام امام حسین (ع)',
                                                'tags' => ['قیام_امام_حسین_(ع)'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '969',
                                        'name' => 'درس 11: ورود اسلام به ایران',
                                        'tags' => ['درس_11:_ورود_اسلام_به_ایران'],

                                        'children' => [
                                            [
                                                'id' => '966',
                                                'name' => 'حملۀ اعراب مسلمان به ایران و سقوط ساسانیان',
                                                'tags' => ['حملۀ_اعراب_مسلمان_به_ایران_و_سقوط_ساسانیان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '967',
                                                'name' => 'ایران در زمان امویان و عباسیان',
                                                'tags' => ['ایران_در_زمان_امویان_و_عباسیان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '968',
                                                'name' => 'ایرانیان مسلمان می‌شوند',
                                                'tags' => ['ایرانیان_مسلمان_می‌شوند'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '973',
                                        'name' => 'درس 12: عصر طلایی فرهنگ و تمدن ایرانی- اسلامی',
                                        'tags' => ['درس_12:_عصر_طلایی_فرهنگ_و_تمدن_ایرانی-_اسلامی'],

                                        'children' => [
                                            [
                                                'id' => '970',
                                                'name' => 'تأسیس سلسله‌های ایرانی',
                                                'tags' => ['تأسیس_سلسله‌های_ایرانی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '971',
                                                'name' => 'ایرانیان، پرچمدار علم و دانش',
                                                'tags' => ['ایرانیان،_پرچمدار_علم_و_دانش'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '972',
                                                'name' => 'زبان و ادبیات و معماری',
                                                'tags' => ['زبان_و_ادبیات_و_معماری'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '977',
                                        'name' => 'درس 13: غزنویان، سلجوقیان و خوارزمشاهیان',
                                        'tags' => ['درس_13:_غزنویان،_سلجوقیان_و_خوارزمشاهیان'],

                                        'children' => [
                                            [
                                                'id' => '974',
                                                'name' => 'غزنویان',
                                                'tags' => ['غزنویان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '975',
                                                'name' => 'سلجوقیان',
                                                'tags' => ['سلجوقیان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '976',
                                                'name' => 'خوارزمشاهیان',
                                                'tags' => ['خوارزمشاهیان'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '981',
                                        'name' => 'درس 14: میراث فرهنگی ایران در عصر سلجوقی',
                                        'tags' => ['درس_14:_میراث_فرهنگی_ایران_در_عصر_سلجوقی'],

                                        'children' => [
                                            [
                                                'id' => '978',
                                                'name' => 'تشکیلات حکومتی',
                                                'tags' => ['تشکیلات_حکومتی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '979',
                                                'name' => 'میراث فرهنگی و تمدنی',
                                                'tags' => ['میراث_فرهنگی_و_تمدنی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '980',
                                                'name' => 'توسعۀ شهرها، معماری و هنر',
                                                'tags' => ['توسعۀ_شهرها،_معماری_و_هنر'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '985',
                                        'name' => 'درس 15: حملۀ چنگیز و تیمور به ایران',
                                        'tags' => ['درس_15:_حملۀ_چنگیز_و_تیمور_به_ایران'],

                                        'children' => [
                                            [
                                                'id' => '982',
                                                'name' => 'مغولان و هجوم آن‌ها به ایران',
                                                'tags' => ['مغولان_و_هجوم_آن‌ها_به_ایران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '983',
                                                'name' => 'حکومت مغولان (ایلخانان) بر ایران و قیام سربداران',
                                                'tags' => ['حکومت_مغولان_(ایلخانان)_بر_ایران_و_قیام_سربداران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '984',
                                                'name' => 'هجوم تیمور به ایران',
                                                'tags' => ['هجوم_تیمور_به_ایران'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '989',
                                        'name' => 'درس 16: پیروزی فرهنگ بر شمشیر',
                                        'tags' => ['درس_16:_پیروزی_فرهنگ_بر_شمشیر'],

                                        'children' => [
                                            [
                                                'id' => '986',
                                                'name' => 'تأثیر فرهنگ ایرانی بر مغولان',
                                                'tags' => ['تأثیر_فرهنگ_ایرانی_بر_مغولان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '987',
                                                'name' => 'توجه ایلخانان به معماری و هنر',
                                                'tags' => ['توجه_ایلخانان_به_معماری_و_هنر'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '988',
                                                'name' => 'علاقه‌مندی جانشینان تیمور به معماری و هنر',
                                                'tags' => ['علاقه‌مندی_جانشینان_تیمور_به_معماری_و_هنر'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '993',
                                        'name' => 'درس 17: ویژگی‌های طبیعی آسیا',
                                        'tags' => ['درس_17:_ویژگی‌های_طبیعی_آسیا'],

                                        'children' => [
                                            [
                                                'id' => '990',
                                                'name' => 'موقعیت و وسعت',
                                                'tags' => ['موقعیت_و_وسعت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '991',
                                                'name' => 'ناهمواری‌ها (اشکال زمین)',
                                                'tags' => ['ناهمواری‌ها_(اشکال_زمین)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '992',
                                                'name' => 'آب‌و‌هوا',
                                                'tags' => ['آب‌و‌هوا'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '999',
                                        'name' => 'درس 18: ویژگی‌های انسانی و اقتصادی آسیا',
                                        'tags' => ['درس_18:_ویژگی‌های_انسانی_و_اقتصادی_آسیا'],

                                        'children' => [
                                            [
                                                'id' => '994',
                                                'name' => 'جمعیت',
                                                'tags' => ['جمعیت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '995',
                                                'name' => 'نژاد و زبان و دین',
                                                'tags' => ['نژاد_و_زبان_و_دین'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '996',
                                                'name' => 'اقتصاد',
                                                'tags' => ['اقتصاد'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '997',
                                                'name' => 'جاذبه‌های گردشگری',
                                                'tags' => ['جاذبه‌های_گردشگری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '998',
                                                'name' => 'استفاده از اطلس',
                                                'tags' => ['استفاده_از_اطلس'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1002',
                                        'name' => 'درس 19: ویژگی‌های منطقۀ جنوب غربی آسیا',
                                        'tags' => ['درس_19:_ویژگی‌های_منطقۀ_جنوب_غربی_آسیا'],

                                        'children' => [
                                            [
                                                'id' => '1000',
                                                'name' => 'موقعیت و ویژگی‌های طبیعی',
                                                'tags' => ['موقعیت_و_ویژگی‌های_طبیعی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1001',
                                                'name' => 'ویژگی‌های انسانی و اقتصادی',
                                                'tags' => ['ویژگی‌های_انسانی_و_اقتصادی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1007',
                                        'name' => 'درس 20: ایران و منطقۀ جنوب غربی آسیا',
                                        'tags' => ['درس_20:_ایران_و_منطقۀ_جنوب_غربی_آسیا'],

                                        'children' => [
                                            [
                                                'id' => '1003',
                                                'name' => 'جنوب غربی آسیا، منطقه‌ای استراتژیک و پرتنش',
                                                'tags' => ['جنوب_غربی_آسیا،_منطقه‌ای_استراتژیک_و_پرتنش'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1004',
                                                'name' => 'جایگاه ایران در منطقه',
                                                'tags' => ['جایگاه_ایران_در_منطقه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1005',
                                                'name' => 'فلسطین، موضوع مهم جهان اسلام',
                                                'tags' => ['فلسطین،_موضوع_مهم_جهان_اسلام'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1006',
                                                'name' => 'مقیاس نقشه و محاسبۀ مسافت‌ها',
                                                'tags' => ['مقیاس_نقشه_و_محاسبۀ_مسافت‌ها'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1014',
                                        'name' => 'درس 21: ویژگی‌های طبیعی و انسانی اروپا',
                                        'tags' => ['درس_21:_ویژگی‌های_طبیعی_و_انسانی_اروپا'],

                                        'children' => [
                                            [
                                                'id' => '1010',
                                                'name' => 'ویژگی‌های طبیعی',
                                                'tags' => ['ویژگی‌های_طبیعی'],
                                                'children' => [
                                                    [
                                                        'id' => '1008',
                                                        'name' => 'موقعیت، وسعت و ناهمواری‌ها',
                                                        'tags' => ['موقعیت،_وسعت_و_ناهمواری‌ها'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1009',
                                                        'name' => 'آب‌و‌هوا و رودها',
                                                        'tags' => ['آب‌و‌هوا_و_رودها'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],
                                            [
                                                'id' => '1013',
                                                'name' => 'ویژگی‌های انسانی',
                                                'tags' => ['ویژگی‌های_انسانی'],
                                                'children' => [
                                                    [
                                                        'id' => '1011',
                                                        'name' => 'جمعیت، نژاد، زبان و دین',
                                                        'tags' => ['جمعیت،_نژاد،_زبان_و_دین'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1012',
                                                        'name' => 'اقتصاد',
                                                        'tags' => ['اقتصاد'],
                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1021',
                                        'name' => 'درس 22: ویژگی‌های طبیعی و انسانی آفریقا',
                                        'tags' => ['درس_22:_ویژگی‌های_طبیعی_و_انسانی_آفریقا'],

                                        'children' => [
                                            [
                                                'id' => '1017',
                                                'name' => 'ویژگی‌های طبیعی',
                                                'tags' => ['ویژگی‌های_طبیعی'],
                                                'children' => [
                                                    [
                                                        'id' => '1015',
                                                        'name' => 'موقعیت، وسعت و ناهمواری‌ها',
                                                        'tags' => ['موقعیت،_وسعت_و_ناهمواری‌ها'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1016',
                                                        'name' => 'آب‌و‌هوا و رودها',
                                                        'tags' => ['آب‌و‌هوا_و_رودها'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],
                                            [
                                                'id' => '1020',
                                                'name' => 'ویژگی‌های انسانی',
                                                'tags' => ['ویژگی‌های_انسانی'],
                                                'children' => [
                                                    [
                                                        'id' => '1018',
                                                        'name' => 'جمعیت، نژاد، زبان و دین',
                                                        'tags' => ['جمعیت،_نژاد،_زبان_و_دین'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1019',
                                                        'name' => 'اقتصاد',
                                                        'tags' => ['اقتصاد'],
                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1028',
                                        'name' => 'درس 23: قارۀ آمریکا',
                                        'tags' => ['درس_23:_قارۀ_آمریکا'],
                                        'children' => [
                                            [
                                                'id' => '1024',
                                                'name' => 'ویژگی‌های طبیعی',
                                                'tags' => ['ویژگی‌های_طبیعی'],
                                                'children' => [
                                                    [
                                                        'id' => '1022',
                                                        'name' => 'موقعیت، وسعت و ناهمواری‌ها',
                                                        'tags' => ['موقعیت،_وسعت_و_ناهمواری‌ها'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1023',
                                                        'name' => 'آب‌و‌هوا و رودها',
                                                        'tags' => ['آب‌و‌هوا_و_رودها'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],
                                            [
                                                'id' => '1027',
                                                'name' => 'ویژگی‌های انسانی',
                                                'tags' => ['ویژگی‌های_انسانی'],
                                                'children' => [
                                                    [
                                                        'id' => '1025',
                                                        'name' => 'جمعیت، نژاد، زبان و دین',
                                                        'tags' => ['جمعیت،_نژاد،_زبان_و_دین'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1026',
                                                        'name' => 'اقتصاد',
                                                        'tags' => ['اقتصاد'],
                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1031',
                                        'name' => 'درس 24: قارۀ استرالیا و اقیانوسیه',
                                        'tags' => ['درس_24:_قارۀ_استرالیا_و_اقیانوسیه'],

                                        'children' => [
                                            [
                                                'id' => '1029',
                                                'name' => 'موقعیت و وسعت و ویژگی‌های طبیعی',
                                                'tags' => ['موقعیت_و_وسعت_و_ویژگی‌های_طبیعی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1030',
                                                'name' => 'ویژگی‌های انسانی و اقتصادی',
                                                'tags' => ['ویژگی‌های_انسانی_و_اقتصادی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '1078',
                                'name' => 'پیام‌های آسمان',
                                'tags' => ['پیام‌های_آسمان'],
                                'children' => [
                                    [
                                        'id' => '1035',
                                        'name' => 'درس اول: آفرینش شگفت‌انگیز',
                                        'tags' => ['درس_اول:_آفرینش_شگفت‌انگیز'],

                                        'children' => [
                                            [
                                                'id' => '1033',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1034',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1038',
                                        'name' => 'درس دوم: عفو و گذشت',
                                        'tags' => ['درس_دوم:_عفو_و_گذشت'],
                                        'children' => [
                                            [
                                                'id' => '1036',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1037',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1041',
                                        'name' => 'درس سوم: همه‌چیز در دست تو',
                                        'tags' => ['درس_سوم:_همه‌چیز_در_دست_تو'],

                                        'children' => [
                                            [
                                                'id' => '1039',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1040',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1044',
                                        'name' => 'درس چهارم: پیوند جاودان',
                                        'tags' => ['درس_چهارم:_پیوند_جاودان'],
                                        'children' => [
                                            [
                                                'id' => '1042',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1043',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1047',
                                        'name' => 'درس پنجم: روزی که اسلام کامل شد',
                                        'tags' => ['درس_پنجم:_روزی_که_اسلام_کامل_شد'],

                                        'children' => [
                                            [
                                                'id' => '1045',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1046',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1050',
                                        'name' => 'درس ششم: نردبان آسمان',
                                        'tags' => ['درس_ششم:_نردبان_آسمان'],
                                        'children' => [
                                            [
                                                'id' => '1048',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1049',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1053',
                                        'name' => 'درس هفتم: یک فرصت طلایی',
                                        'tags' => ['درس_هفتم:_یک_فرصت_طلایی'],
                                        'children' => [
                                            [
                                                'id' => '1051',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1052',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1056',
                                        'name' => 'درس هشتم: نشان ارزشمندی',
                                        'tags' => ['درس_هشتم:_نشان_ارزشمندی'],
                                        'children' => [
                                            [
                                                'id' => '1054',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1055',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1059',
                                        'name' => 'درس نهم: تدبیر زندگانی',
                                        'tags' => ['درس_نهم:_تدبیر_زندگانی'],
                                        'children' => [
                                            [
                                                'id' => '1057',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1058',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1062',
                                        'name' => 'درس دهم: دو سرمایۀ گران‌بها',
                                        'tags' => ['درس_دهم:_دو_سرمایۀ_گران‌بها'],

                                        'children' => [
                                            [
                                                'id' => '1060',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1061',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1065',
                                        'name' => 'درس یازدهم: آفت‌های زبان',
                                        'tags' => ['درس_یازدهم:_آفت‌های_زبان'],
                                        'children' => [
                                            [
                                                'id' => '1063',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1064',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1068',
                                        'name' => 'درس دوازدهم: ارزش کار',
                                        'tags' => ['درس_دوازدهم:_ارزش_کار'],
                                        'children' => [
                                            [
                                                'id' => '1066',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1067',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1071',
                                        'name' => 'درس سیزدهم: کلید گنج‌ها',
                                        'tags' => ['درس_سیزدهم:_کلید_گنج‌ها'],
                                        'children' => [
                                            [
                                                'id' => '1069',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1070',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1074',
                                        'name' => 'درس چهاردهم: ما مسلمانان',
                                        'tags' => ['درس_چهاردهم:_ما_مسلمانان'],
                                        'children' => [
                                            [
                                                'id' => '1072',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1073',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1077',
                                        'name' => 'درس پانزدهم: حق‌الناس',
                                        'tags' => ['درس_پانزدهم:_حق‌الناس'],
                                        'children' => [
                                            [
                                                'id' => '1075',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1076',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],

                        ],
                    ],
                    [
                        'id' => '1612',
                        'name' => 'نهم',
                        'tags' => ['نهم'],
                        'children' => [
                            [
                                'id' => '1116',
                                'name' => 'ریاضی',
                                'tags' => ['ریاضی'],
                                'children' => [
                                    [
                                        'id' => '1084',
                                        'name' => 'فصل 1: مجموعه‌ها',
                                        'tags' => ['فصل_1:_مجموعه‌ها'],
                                        'children' => [
                                            [
                                                'id' => '1080',
                                                'name' => 'درس اول: معرفی مجموعه',
                                                'tags' => ['درس_اول:_معرفی_مجموعه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1081',
                                                'name' => 'درس دوم: مجموعه‌های برابر و نمایش مجموعه‌ها',
                                                'tags' => ['درس_دوم:_مجموعه‌های_برابر_و_نمایش_مجموعه‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1082',
                                                'name' => 'درس سوم: اجتماع، اشتراک و تفاضلِ مجموعه‌ها',
                                                'tags' => ['درس_سوم:_اجتماع،_اشتراک_و_تفاضلِ_مجموعه‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1083',
                                                'name' => 'درس چهارم: مجموعه‌ها و احتمال',
                                                'tags' => ['درس_چهارم:_مجموعه‌ها_و_احتمال'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1088',
                                        'name' => 'فصل 2: عددهای حقیقی',
                                        'tags' => ['فصل_2:_عددهای_حقیقی'],
                                        'children' => [
                                            [
                                                'id' => '1085',
                                                'name' => 'درس اول: عددهای گویا',
                                                'tags' => ['درس_اول:_عددهای_گویا'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1086',
                                                'name' => 'درس دوم: عددهای حقیقی',
                                                'tags' => ['درس_دوم:_عددهای_حقیقی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1087',
                                                'name' => 'درس سوم: قدر مطلق و محاسبۀ تقریبی',
                                                'tags' => ['درس_سوم:_قدر_مطلق_و_محاسبۀ_تقریبی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1094',
                                        'name' => 'فصل 3: استدلال و اثبات در هندسه',
                                        'tags' => ['فصل_3:_استدلال_و_اثبات_در_هندسه'],

                                        'children' => [
                                            [
                                                'id' => '1089',
                                                'name' => 'درس اول: استدلال',
                                                'tags' => ['درس_اول:_استدلال'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1090',
                                                'name' => 'درس دوم: آشنایی با اثبات در هندسه',
                                                'tags' => ['درس_دوم:_آشنایی_با_اثبات_در_هندسه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1091',
                                                'name' => 'درس سوم: همنهشتی مثلث‌ها',
                                                'tags' => ['درس_سوم:_همنهشتی_مثلث‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1092',
                                                'name' => 'درس چهارم: حل مسئله در هندسه',
                                                'tags' => ['درس_چهارم:_حل_مسئله_در_هندسه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1093',
                                                'name' => 'درس پنجم: شکل‌های متشابه',
                                                'tags' => ['درس_پنجم:_شکل‌های_متشابه'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1099',
                                        'name' => 'فصل 4: توان و ریشه',
                                        'tags' => ['فصل_4:_توان_و_ریشه'],
                                        'children' => [
                                            [
                                                'id' => '1095',
                                                'name' => 'درس اول: توان صحیح',
                                                'tags' => ['درس_اول:_توان_صحیح'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1096',
                                                'name' => 'درس دوم: نماد علمی',
                                                'tags' => ['درس_دوم:_نماد_علمی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1097',
                                                'name' => 'درس سوم: ریشه‌گیری',
                                                'tags' => ['درس_سوم:_ریشه‌گیری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1098',
                                                'name' => 'درس چهارم: جمع و تفریق رادیکال‌ها',
                                                'tags' => ['درس_چهارم:_جمع_و_تفریق_رادیکال‌ها'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1103',
                                        'name' => 'فصل 5: عبارت‌های جبری',
                                        'tags' => ['فصل_5:_عبارت‌های_جبری'],
                                        'children' => [
                                            [
                                                'id' => '1100',
                                                'name' => 'درس اول: عبارت‌های جبری و مفهوم اتحاد',
                                                'tags' => ['درس_اول:_عبارت‌های_جبری_و_مفهوم_اتحاد'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1101',
                                                'name' => 'درس دوم: چند اتحاد دیگر، تجزیه و کاربرد‌ها',
                                                'tags' => ['درس_دوم:_چند_اتحاد_دیگر،_تجزیه_و_کاربرد‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1102',
                                                'name' => 'درس سوم: نابرابری‌ها و نامعادله‌ها',
                                                'tags' => ['درس_سوم:_نابرابری‌ها_و_نامعادله‌ها'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1107',
                                        'name' => 'فصل 6: خط و معادله‌های خطی',
                                        'tags' => ['فصل_6:_خط_و_معادله‌های_خطی'],

                                        'children' => [
                                            [
                                                'id' => '1104',
                                                'name' => 'درس اول: معادلۀ خط',
                                                'tags' => ['درس_اول:_معادلۀ_خط'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1105',
                                                'name' => 'درس دوم: شیب خط و عرض از مبدأ',
                                                'tags' => ['درس_دوم:_شیب_خط_و_عرض_از_مبدأ'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1106',
                                                'name' => 'درس سوم: دستگاه معادله‌های خطی',
                                                'tags' => ['درس_سوم:_دستگاه_معادله‌های_خطی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1111',
                                        'name' => 'فصل 7: عبارت‌های گویا',
                                        'tags' => ['فصل_7:_عبارت‌های_گویا'],
                                        'children' => [
                                            [
                                                'id' => '1108',
                                                'name' => 'درس اول: معرفی و ساده‌ کردن عبارت‌های گویا',
                                                'tags' => ['درس_اول:_معرفی_و_ساده‌_کردن_عبارت‌های_گویا'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1109',
                                                'name' => 'درس دوم: محاسبات عبارت‌های گویا',
                                                'tags' => ['درس_دوم:_محاسبات_عبارت‌های_گویا'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1110',
                                                'name' => 'درس سوم: تقسیم چند‌جمله‌ای‌ها',
                                                'tags' => ['درس_سوم:_تقسیم_چند‌جمله‌ای‌ها'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1115',
                                        'name' => 'فصل 8: حجم و مساحت',
                                        'tags' => ['فصل_8:_حجم_و_مساحت'],
                                        'children' => [
                                            [
                                                'id' => '1112',
                                                'name' => 'درس اول: حجم و مساحت کره',
                                                'tags' => ['درس_اول:_حجم_و_مساحت_کره'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1113',
                                                'name' => 'درس دوم: حجم هرم و مخروط',
                                                'tags' => ['درس_دوم:_حجم_هرم_و_مخروط'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1114',
                                                'name' => 'درس سوم: سطح و حجم',
                                                'tags' => ['درس_سوم:_سطح_و_حجم'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '1180',
                                'name' => 'زبان انگلیسی',
                                'tags' => ['زبان_انگلیسی'],
                                'children' => [
                                    [
                                        'id' => '1125',
                                        'name' => 'Lesson 1: Personality',
                                        'tags' => ['Lesson_1:_Personality'],
                                        'children' => [
                                            [
                                                'id' => '1117',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1118',
                                                'name' => '(Language Melody (Intonation',
                                                'tags' => ['(Language_Melody_(Intonation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1119',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1120',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1121',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1122',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1123',
                                                'name' => 'cloze',
                                                'tags' => ['cloze'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1124',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1134',
                                        'name' => 'Lesson 2: Travel',
                                        'tags' => ['Lesson_2:_Travel'],
                                        'children' => [
                                            [
                                                'id' => '1126',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1127',
                                                'name' => '(Language Melody (Intonation',
                                                'tags' => ['(Language_Melody_(Intonation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1128',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1129',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1130',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1131',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1132',
                                                'name' => 'cloze',
                                                'tags' => ['cloze'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1133',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1143',
                                        'name' => 'Lesson 3: Festivals and Ceremonies',
                                        'tags' => ['Lesson_3:_Festivals_and_Ceremonies'],

                                        'children' => [
                                            [
                                                'id' => '1135',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1136',
                                                'name' => '(Language Melody (Intonation',
                                                'tags' => ['(Language_Melody_(Intonation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1137',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1138',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1139',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1140',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1141',
                                                'name' => 'cloze',
                                                'tags' => ['cloze'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1142',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1152',
                                        'name' => 'Lesson 4: Service',
                                        'tags' => ['Lesson_4:_Service'],
                                        'children' => [
                                            [
                                                'id' => '1144',
                                                'name' => '(Language Melody (Intonation',
                                                'tags' => ['(Language_Melody_(Intonation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1145',
                                                'name' => 'Cloze',
                                                'tags' => ['Cloze'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1146',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1147',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1148',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1149',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1150',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1151',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1161',
                                        'name' => 'Lesson 5: Media',
                                        'tags' => ['Lesson_5:_Media'],
                                        'children' => [
                                            [
                                                'id' => '1153',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1154',
                                                'name' => '(Language Melody (Intonation',
                                                'tags' => ['(Language_Melody_(Intonation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1155',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1156',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1157',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1158',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1159',
                                                'name' => 'cloze',
                                                'tags' => ['cloze'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1160',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1170',
                                        'name' => 'Lesson 6: Health and Injuries',
                                        'tags' => ['Lesson_6:_Health_and_Injuries'],

                                        'children' => [
                                            [
                                                'id' => '1162',
                                                'name' => 'Vocabulary',
                                                'tags' => ['Vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1163',
                                                'name' => '(Language Melody (Intonation',
                                                'tags' => ['(Language_Melody_(Intonation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1164',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1165',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1166',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1167',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1168',
                                                'name' => 'cloze',
                                                'tags' => ['cloze'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1169',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1179',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '1171',
                                                'name' => 'Reading',
                                                'tags' => ['Reading'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1172',
                                                'name' => 'Writing',
                                                'tags' => ['Writing'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1173',
                                                'name' => '(Language Melody (Intonation',
                                                'tags' => ['(Language_Melody_(Intonation'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1174',
                                                'name' => 'Conversation',
                                                'tags' => ['Conversation'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1175',
                                                'name' => 'cloze',
                                                'tags' => ['cloze'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1176',
                                                'name' => 'vocabulary',
                                                'tags' => ['vocabulary'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1177',
                                                'name' => 'Grammar',
                                                'tags' => ['Grammar'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1178',
                                                'name' => 'Spelling',
                                                'tags' => ['Spelling'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '1225',
                                'name' => 'عربی',
                                'tags' => ['عربی'],
                                'children' => [
                                    [
                                        'id' => '1184',
                                        'name' => 'الدرس الأول: مراجعة دروس الصف السابع و الثامن',
                                        'tags' => ['الدرس_الأول:_مراجعة_دروس_الصف_السابع_و_الثامن'],

                                        'children' => [
                                            [
                                                'id' => '1181',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1182',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1183',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1188',
                                        'name' => 'الدرس الثانی: العبور الآمن',
                                        'tags' => ['الدرس_الثانی:_العبور_الآمن'],

                                        'children' => [
                                            [
                                                'id' => '1185',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1186',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1187',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1192',
                                        'name' => 'الدرس الثالث: جسر الصداقة',
                                        'tags' => ['الدرس_الثالث:_جسر_الصداقة'],

                                        'children' => [
                                            [
                                                'id' => '1189',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1190',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1191',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1196',
                                        'name' => 'الدرس الرابع: الصبر مفتاح الفرج',
                                        'tags' => ['الدرس_الرابع:_الصبر_مفتاح_الفرج'],

                                        'children' => [
                                            [
                                                'id' => '1193',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1194',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1195',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1200',
                                        'name' => 'الدرس الخامس: الرجاء',
                                        'tags' => ['الدرس_الخامس:_الرجاء'],
                                        'children' => [
                                            [
                                                'id' => '1197',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1198',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1199',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1204',
                                        'name' => 'الدرس السادس: تغییر الحیاة',
                                        'tags' => ['الدرس_السادس:_تغییر_الحیاة'],

                                        'children' => [
                                            [
                                                'id' => '1201',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1202',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1203',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1208',
                                        'name' => 'الدرس السابع: ثمرة الجد',
                                        'tags' => ['الدرس_السابع:_ثمرة_الجد'],
                                        'children' => [
                                            [
                                                'id' => '1205',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1206',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1207',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1212',
                                        'name' => 'الدرس الثامن: حوار بین الزائر و سائق سیارة الأجرة',
                                        'tags' => ['الدرس_الثامن:_حوار_بین_الزائر_و_سائق_سیارة_الأجرة'],

                                        'children' => [
                                            [
                                                'id' => '1209',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1210',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1211',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1216',
                                        'name' => 'الدرس التاسع: نصوص حول الصحة',
                                        'tags' => ['الدرس_التاسع:_نصوص_حول_الصحة'],

                                        'children' => [
                                            [
                                                'id' => '1213',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1214',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1215',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1220',
                                        'name' => 'الدرس العاشر: الأمانة',
                                        'tags' => ['الدرس_العاشر:_الأمانة'],
                                        'children' => [
                                            [
                                                'id' => '1217',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1218',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1219',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1224',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '1221',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1222',
                                                'name' => 'ترجمۀ عبارات',
                                                'tags' => ['ترجمۀ_عبارات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1223',
                                                'name' => 'قواعد',
                                                'tags' => ['قواعد'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '1333',
                                'name' => 'علوم',
                                'tags' => ['علوم'],
                                'children' => [
                                    [
                                        'id' => '1230',
                                        'name' => 'فصل اول: مواد و نقش آن‌ها در زندگی',
                                        'tags' => ['فصل_اول:_مواد_و_نقش_آن‌ها_در_زندگی'],

                                        'children' => [
                                            [
                                                'id' => '1226',
                                                'name' => 'ویژگی‌ها و کاربرد فلزات',
                                                'tags' => ['ویژگی‌ها_و_کاربرد_فلزات'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1227',
                                                'name' => 'ویژگی‌ها و کاربرد نافلزات',
                                                'tags' => ['ویژگی‌ها_و_کاربرد_نافلزات'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1228',
                                                'name' => 'طبقه‌بندی عناصر بر‌اساس آرایش الکترونی',
                                                'tags' => ['طبقه‌بندی_عناصر_بر‌اساس_آرایش_الکترونی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1229',
                                                'name' => 'بسپارهای طبیعی و مصنوعی',
                                                'tags' => ['بسپارهای_طبیعی_و_مصنوعی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1234',
                                        'name' => 'فصل دوم: رفتار اتم‌ها با یکدیگر',
                                        'tags' => ['فصل_دوم:_رفتار_اتم‌ها_با_یکدیگر'],

                                        'children' => [
                                            [
                                                'id' => '1231',
                                                'name' => 'ذره‌های سازندۀ مواد',
                                                'tags' => ['ذره‌های_سازندۀ_مواد'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1232',
                                                'name' => 'مبادلۀ الکترونی - پیوند یونی',
                                                'tags' => ['مبادلۀ_الکترونی_-_پیوند_یونی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1233',
                                                'name' => 'اشتراک الکترون ها - پیوند کووالانسی',
                                                'tags' => ['اشتراک_الکترون_ها_-_پیوند_کووالانسی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1241',
                                        'name' => 'فصل سوم: به دنبال محیطی بهتر برای زندگی',
                                        'tags' => ['فصل_سوم:_به_دنبال_محیطی_بهتر_برای_زندگی'],

                                        'children' => [
                                            [
                                                'id' => '1235',
                                                'name' => 'چرخه',
                                                'tags' => ['چرخه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1236',
                                                'name' => 'نفت خام و کاربردها',
                                                'tags' => ['نفت_خام_و_کاربردها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1237',
                                                'name' => 'هیدروکربن ها',
                                                'tags' => ['هیدروکربن_ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1238',
                                                'name' => 'اتن و واکنش پلیمری شدن (بسپارشی)',
                                                'tags' => ['اتن_و_واکنش_پلیمری_شدن_(بسپارشی)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1239',
                                                'name' => 'واکنش سوختن و تولید کربن دی اکسید',
                                                'tags' => ['واکنش_سوختن_و_تولید_کربن_دی_اکسید'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1240',
                                                'name' => 'پلاستیک ها؛ معایب و مزایا',
                                                'tags' => ['پلاستیک_ها؛_معایب_و_مزایا'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1249',
                                        'name' => 'فصل چهارم: حرکت چیست',
                                        'tags' => ['فصل_چهارم:_حرکت_چیست'],
                                        'children' => [
                                            [
                                                'id' => '1242',
                                                'name' => 'حرکت در همه‌چیز و همه‌جا',
                                                'tags' => ['حرکت_در_همه‌چیز_و_همه‌جا'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1243',
                                                'name' => 'مسافت و جا‌به‌جایی',
                                                'tags' => ['مسافت_و_جا‌به‌جایی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1244',
                                                'name' => 'تندی و سرعت',
                                                'tags' => ['تندی_و_سرعت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1245',
                                                'name' => 'حرکت یکنواخت',
                                                'tags' => ['حرکت_یکنواخت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1246',
                                                'name' => 'شتاب متوسط',
                                                'tags' => ['شتاب_متوسط'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1247',
                                                'name' => 'حرکت شتابدار با شتاب ثابت',
                                                'tags' => ['حرکت_شتابدار_با_شتاب_ثابت'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1248',
                                                'name' => 'نمودارهای حرکت',
                                                'tags' => ['نمودارهای_حرکت'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1260',
                                        'name' => 'فصل پنجم: نیرو',
                                        'tags' => ['فصل_پنجم:_نیرو'],
                                        'children' => [
                                            [
                                                'id' => '1250',
                                                'name' => 'تعریف نیرو و اثرات آن',
                                                'tags' => ['تعریف_نیرو_و_اثرات_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1251',
                                                'name' => 'ریاضیات حاکم بر نیروها (نیروی خالص)',
                                                'tags' => ['ریاضیات_حاکم_بر_نیروها_(نیروی_خالص)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1252',
                                                'name' => 'قانون اول نیوتون (نیروهای متوازن)',
                                                'tags' => ['قانون_اول_نیوتون_(نیروهای_متوازن)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1253',
                                                'name' => 'قانون دوم نیوتون (نیروهای خالص عامل شتاب)',
                                                'tags' => ['قانون_دوم_نیوتون_(نیروهای_خالص_عامل_شتاب)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1254',
                                                'name' => 'قانون سوم نیوتون (نیروی کنش و واکنش)',
                                                'tags' => ['قانون_سوم_نیوتون_(نیروی_کنش_و_واکنش)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1259',
                                                'name' => 'نیروهای خاص',
                                                'tags' => ['نیروهای_خاص'],
                                                'children' => [
                                                    [
                                                        'id' => '1255',
                                                        'name' => 'نیروی گرانش (قانون جهانی گرانش)',
                                                        'tags' => ['نیروی_گرانش_(قانون_جهانی_گرانش)'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1256',
                                                        'name' => 'وزن',
                                                        'tags' => ['وزن'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1257',
                                                        'name' => 'عمودی تکیه‌گاه (سطح)',
                                                        'tags' => ['عمودی_تکیه‌گاه_(سطح)'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1258',
                                                        'name' => 'نیروی اصطکاک',
                                                        'tags' => ['نیروی_اصطکاک'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1266',
                                        'name' => 'فصل ششم: زمین‌ساخت ورقه‌ای',
                                        'tags' => ['فصل_ششم:_زمین‌ساخت_ورقه‌ای'],

                                        'children' => [
                                            [
                                                'id' => '1261',
                                                'name' => 'قاره‌های متحرک',
                                                'tags' => ['قاره‌های_متحرک'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1262',
                                                'name' => 'زمین ساخت ورقه‌ای',
                                                'tags' => ['زمین_ساخت_ورقه‌ای'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1263',
                                                'name' => 'فرضیۀ گسترش بستر اقیانوس‌ها',
                                                'tags' => ['فرضیۀ_گسترش_بستر_اقیانوس‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1264',
                                                'name' => 'حرکت ورقه‌های سنگ‌کره',
                                                'tags' => ['حرکت_ورقه‌های_سنگ‌کره'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1265',
                                                'name' => 'پیامدهای حرکت ورقه‌های سنگ‌کره',
                                                'tags' => ['پیامدهای_حرکت_ورقه‌های_سنگ‌کره'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1270',
                                        'name' => 'فصل هفتم: آثاری از گذشتۀ زمین',
                                        'tags' => ['فصل_هفتم:_آثاری_از_گذشتۀ_زمین'],

                                        'children' => [
                                            [
                                                'id' => '1267',
                                                'name' => 'فسیل و شرایط لازم برای تشکیل آن',
                                                'tags' => ['فسیل_و_شرایط_لازم_برای_تشکیل_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1268',
                                                'name' => 'راه‌های تشکیل فسیل',
                                                'tags' => ['راه‌های_تشکیل_فسیل'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1269',
                                                'name' => 'کاربرد فسیل‌ها',
                                                'tags' => ['کاربرد_فسیل‌ها'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1276',
                                        'name' => 'فصل هشتم: فشار و آثار آن',
                                        'tags' => ['فصل_هشتم:_فشار__و_آثار_آن'],

                                        'children' => [
                                            [
                                                'id' => '1271',
                                                'name' => 'تعریف فشار و واحدهای آن',
                                                'tags' => ['تعریف_فشار_و_واحدهای_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1272',
                                                'name' => 'فشار در جامدات',
                                                'tags' => ['فشار_در_جامدات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1273',
                                                'name' => 'فشار در مایعات',
                                                'tags' => ['فشار_در_مایعات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1274',
                                                'name' => 'اصل پاسکال',
                                                'tags' => ['اصل_پاسکال'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1275',
                                                'name' => 'فشار در گازها',
                                                'tags' => ['فشار_در_گازها'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1287',
                                        'name' => 'فصل نهم: ماشین‌ها',
                                        'tags' => ['فصل_نهم:_ماشین‌ها'],
                                        'children' => [
                                            [
                                                'id' => '1277',
                                                'name' => 'کار و عوامل مؤثر بر آن',
                                                'tags' => ['کار_و_عوامل_مؤثر_بر_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1278',
                                                'name' => 'تعریف ماشین و روش‌های کمک کردن آن',
                                                'tags' => ['تعریف_ماشین_و_روش‌های_کمک_کردن_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1279',
                                                'name' => 'گشتاور نیرو',
                                                'tags' => ['گشتاور_نیرو'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1280',
                                                'name' => 'حالت تعادل',
                                                'tags' => ['حالت_تعادل'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1281',
                                                'name' => 'مزیت مکانیکی',
                                                'tags' => ['مزیت_مکانیکی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1282',
                                                'name' => 'اهرم‌ها',
                                                'tags' => ['اهرم‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1283',
                                                'name' => 'قرقره‌ها',
                                                'tags' => ['قرقره‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1284',
                                                'name' => 'چرخ‌دنده‌ها',
                                                'tags' => ['چرخ‌دنده‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1285',
                                                'name' => 'سطح شیبدار',
                                                'tags' => ['سطح_شیبدار'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1286',
                                                'name' => 'ماشین‌های مرکب',
                                                'tags' => ['ماشین‌های_مرکب'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1297',
                                        'name' => 'فصل دهم: نگاهی به فضا',
                                        'tags' => ['فصل_دهم:_نگاهی_به_فضا'],
                                        'children' => [
                                            [
                                                'id' => '1288',
                                                'name' => 'علم نجوم',
                                                'tags' => ['علم_نجوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1289',
                                                'name' => 'کهکشان',
                                                'tags' => ['کهکشان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1290',
                                                'name' => 'ستارگان و خورشید',
                                                'tags' => ['ستارگان_و_خورشید'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1291',
                                                'name' => 'صورت‌های فلکی',
                                                'tags' => ['صورت‌های_فلکی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1296',
                                                'name' => 'منظومۀ شمسی',
                                                'tags' => ['منظومۀ_شمسی'],
                                                'children' => [
                                                    [
                                                        'id' => '1292',
                                                        'name' => 'سیارات',
                                                        'tags' => ['سیارات'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1293',
                                                        'name' => 'قمر',
                                                        'tags' => ['قمر'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1294',
                                                        'name' => 'سیارک',
                                                        'tags' => ['سیارک'],
                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1295',
                                                        'name' => 'شهاب و شهاب سنگ',
                                                        'tags' => ['شهاب_و_شهاب_سنگ'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1303',
                                        'name' => 'فصل یازدهم: گوناگونی جانداران',
                                        'tags' => ['فصل_یازدهم:_گوناگونی_جانداران'],

                                        'children' => [
                                            [
                                                'id' => '1298',
                                                'name' => 'طبقه‌بندی جانداران',
                                                'tags' => ['طبقه‌بندی_جانداران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1299',
                                                'name' => 'باکتری‌ها',
                                                'tags' => ['باکتری‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1300',
                                                'name' => 'آغازیان',
                                                'tags' => ['آغازیان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1301',
                                                'name' => 'قارچ‌ها',
                                                'tags' => ['قارچ‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1302',
                                                'name' => 'ویروس‌ها',
                                                'tags' => ['ویروس‌ها'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1312',
                                        'name' => 'فصل دوازدهم: دنیای گیاهان',
                                        'tags' => ['فصل_دوازدهم:_دنیای_گیاهان'],

                                        'children' => [
                                            [
                                                'id' => '1304',
                                                'name' => 'آوندها در گیاهان',
                                                'tags' => ['آوندها_در_گیاهان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1305',
                                                'name' => 'ریشه و تارهای کشنده',
                                                'tags' => ['ریشه_و_تارهای_کشنده'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1306',
                                                'name' => 'ساقه و برگ',
                                                'tags' => ['ساقه_و_برگ'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1307',
                                                'name' => 'سرخس‌ها',
                                                'tags' => ['سرخس‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1308',
                                                'name' => 'بازدانگان',
                                                'tags' => ['بازدانگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1309',
                                                'name' => 'نهان‌دانگان',
                                                'tags' => ['نهان‌دانگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1310',
                                                'name' => 'خزه‌ها',
                                                'tags' => ['خزه‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1311',
                                                'name' => 'گیاهان در زندگی ما',
                                                'tags' => ['گیاهان_در_زندگی_ما'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1320',
                                        'name' => 'فصل سیزدهم: جانوران بی‌مهره',
                                        'tags' => ['فصل_سیزدهم:_جانوران_بی‌مهره'],

                                        'children' => [
                                            [
                                                'id' => '1313',
                                                'name' => 'گوناگونی جانوران',
                                                'tags' => ['گوناگونی_جانوران'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1314',
                                                'name' => 'اسفنج‌ها',
                                                'tags' => ['اسفنج‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1315',
                                                'name' => 'کیسه‌تنان',
                                                'tags' => ['کیسه‌تنان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1316',
                                                'name' => 'کرم‌ها',
                                                'tags' => ['کرم‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1317',
                                                'name' => 'نرم تنان',
                                                'tags' => ['نرم_تنان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1318',
                                                'name' => 'بند‌پایان',
                                                'tags' => ['بند‌پایان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1319',
                                                'name' => 'خارپوستان',
                                                'tags' => ['خارپوستان'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1327',
                                        'name' => 'فصل چهاردهم: جانوران مهره‌دار',
                                        'tags' => ['فصل_چهاردهم:_جانوران_مهره‌دار'],

                                        'children' => [
                                            [
                                                'id' => '1321',
                                                'name' => 'جانورانی با ستون مهره',
                                                'tags' => ['جانورانی_با_ستون_مهره'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1322',
                                                'name' => 'ماهی‌ها',
                                                'tags' => ['ماهی‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1323',
                                                'name' => 'دوزیستان',
                                                'tags' => ['دوزیستان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1324',
                                                'name' => 'خزندگان',
                                                'tags' => ['خزندگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1325',
                                                'name' => 'پرندگان',
                                                'tags' => ['پرندگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1326',
                                                'name' => 'پستانداران',
                                                'tags' => ['پستانداران'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1332',
                                        'name' => 'فصل پانزدهم: با هم زیستن',
                                        'tags' => ['فصل_پانزدهم:_با_هم_زیستن'],
                                        'children' => [
                                            [
                                                'id' => '1328',
                                                'name' => 'بوم سازگان',
                                                'tags' => ['بوم_سازگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1329',
                                                'name' => 'هرم ماده و انرژی',
                                                'tags' => ['هرم_ماده_و_انرژی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1330',
                                                'name' => 'روابط بین جانداران',
                                                'tags' => ['روابط_بین_جانداران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1331',
                                                'name' => 'تنوع زیستی و اهمیت آن',
                                                'tags' => ['تنوع_زیستی_و_اهمیت_آن'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '1463',
                                'name' => 'فارسی',
                                'tags' => ['فارسی'],
                                'children' => [
                                    [
                                        'id' => '1340',
                                        'name' => 'درس اول: آفرینش همه تنبیه خداوند دل است',
                                        'tags' => ['درس_اول:_آفرینش_همه_تنبیه_خداوند_دل_است'],

                                        'children' => [
                                            [
                                                'id' => '1334',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1335',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1336',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1337',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1338',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1339',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1348',
                                        'name' => 'درس دوم: عجایبِ صنعِ حق‌تعالی',
                                        'tags' => ['درس_دوم:_عجایبِ_صنعِ_حق‌تعالی'],

                                        'children' => [
                                            [
                                                'id' => '1341',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1342',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1343',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1344',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1345',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1346',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1347',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1355',
                                        'name' => 'درس سوم: مثل آیینه، کار و شایستگی',
                                        'tags' => ['درس_سوم:_مثل_آیینه،_کار_و_شایستگی'],

                                        'children' => [
                                            [
                                                'id' => '1349',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1350',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1351',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1352',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1353',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1354',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1362',
                                        'name' => 'درس چهارم: همنشین',
                                        'tags' => ['درس_چهارم:_همنشین'],
                                        'children' => [
                                            [
                                                'id' => '1356',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1357',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1358',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1359',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1360',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1361',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1369',
                                        'name' => 'درس ششم: آداب زندگانی',
                                        'tags' => ['درس_ششم:_آداب_زندگانی'],
                                        'children' => [
                                            [
                                                'id' => '1363',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1364',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1365',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1366',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1367',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1368',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1376',
                                        'name' => 'درس هفتم: پرتو امید',
                                        'tags' => ['درس_هفتم:_پرتو_امید'],
                                        'children' => [
                                            [
                                                'id' => '1370',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1371',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1372',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1373',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1374',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1375',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1384',
                                        'name' => 'درس هشتم: همزیستی با مامِ میهن',
                                        'tags' => ['درس_هشتم:_همزیستی_با_مامِ_میهن'],

                                        'children' => [
                                            [
                                                'id' => '1377',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1378',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1379',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1380',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1381',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1382',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1383',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1391',
                                        'name' => 'درس نهم: راز موفقیت',
                                        'tags' => ['درس_نهم:_راز_موفقیت'],
                                        'children' => [
                                            [
                                                'id' => '1385',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1386',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1387',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1388',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1389',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1390',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1398',
                                        'name' => 'درس دهم: آرشی دیگر',
                                        'tags' => ['درس_دهم:_آرشی_دیگر'],
                                        'children' => [
                                            [
                                                'id' => '1392',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1393',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1394',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1395',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1396',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1397',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1405',
                                        'name' => 'درس یازدهم: زنِ پارسا',
                                        'tags' => ['درس_یازدهم:_زنِ_پارسا'],
                                        'children' => [
                                            [
                                                'id' => '1399',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1400',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1401',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1402',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1403',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1404',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1412',
                                        'name' => 'درس دوازدهم: پیام‌آور رحمت',
                                        'tags' => ['درس_دوازدهم:_پیام‌آور_رحمت'],

                                        'children' => [
                                            [
                                                'id' => '1406',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1407',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1408',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1409',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1410',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1411',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1419',
                                        'name' => 'درس سیزدهم: آشنای غریبان، میلاد گل',
                                        'tags' => ['درس_سیزدهم:_آشنای_غریبان،_میلاد_گل'],

                                        'children' => [
                                            [
                                                'id' => '1413',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1414',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1415',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1416',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1417',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1418',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1427',
                                        'name' => 'درس چهاردهم: پیدای پنهان',
                                        'tags' => ['درس_چهاردهم:_پیدای_پنهان'],
                                        'children' => [
                                            [
                                                'id' => '1420',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1421',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1422',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1423',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1424',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1425',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1426',
                                                'name' => 'حفظ شعر',
                                                'tags' => ['حفظ_شعر'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1434',
                                        'name' => 'درس شانزدهم: آرزو',
                                        'tags' => ['درس_شانزدهم:_آرزو'],
                                        'children' => [
                                            [
                                                'id' => '1428',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1429',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1430',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1431',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1432',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1433',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1441',
                                        'name' => 'درس هفدهم: شازده کوچولو',
                                        'tags' => ['درس_هفدهم:_شازده_کوچولو'],
                                        'children' => [
                                            [
                                                'id' => '1435',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1436',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1437',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1438',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1439',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1440',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1448',
                                        'name' => 'ستایش: به نام خداوند جان و خرد',
                                        'tags' => ['ستایش:_به_نام_خداوند_جان_و_خرد'],

                                        'children' => [
                                            [
                                                'id' => '1442',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1443',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1444',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1445',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1446',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1447',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1455',
                                        'name' => 'نیایش: بیا تا برآریم، دستی ز دل',
                                        'tags' => ['نیایش:_بیا_تا_برآریم،_دستی_ز_دل'],

                                        'children' => [
                                            [
                                                'id' => '1449',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1450',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1451',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1452',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1453',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1454',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1462',
                                        'name' => 'محتوای ترکیبی',
                                        'tags' => ['محتوای_ترکیبی'],
                                        'children' => [
                                            [
                                                'id' => '1456',
                                                'name' => 'واژگان',
                                                'tags' => ['واژگان'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1457',
                                                'name' => 'املا',
                                                'tags' => ['املا'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1458',
                                                'name' => 'تاریخ ادبیات',
                                                'tags' => ['تاریخ_ادبیات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1459',
                                                'name' => 'دانش ادبی',
                                                'tags' => ['دانش_ادبی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1460',
                                                'name' => 'دانش زبانی',
                                                'tags' => ['دانش_زبانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1461',
                                                'name' => 'معنی و مفهوم',
                                                'tags' => ['معنی_و_مفهوم'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '1574',
                                'name' => 'مطالعات اجتماعی',
                                'tags' => ['مطالعات_اجتماعی'],
                                'children' => [
                                    [
                                        'id' => '1467',
                                        'name' => 'درس 1: گوی آبی زیبا',
                                        'tags' => ['درس_1:_گوی_آبی_زیبا'],
                                        'children' => [
                                            [
                                                'id' => '1464',
                                                'name' => 'جایگاه زمین در کیهان',
                                                'tags' => ['جایگاه_زمین_در_کیهان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1465',
                                                'name' => 'موقعیت مکانی',
                                                'tags' => ['موقعیت_مکانی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1466',
                                                'name' => 'طول و عرض جغرافیایی (مختصات جغرافیایی)',
                                                'tags' => ['طول_و_عرض_جغرافیایی_(مختصات_جغرافیایی)'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1470',
                                        'name' => 'درس 2: حرکات زمین',
                                        'tags' => ['درس_2:_حرکات_زمین'],
                                        'children' => [
                                            [
                                                'id' => '1468',
                                                'name' => 'حرکت وضعی',
                                                'tags' => ['حرکت_وضعی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1469',
                                                'name' => 'حرکت انتقالی',
                                                'tags' => ['حرکت_انتقالی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1476',
                                        'name' => 'درس 3: چهرۀ زمین',
                                        'tags' => ['درس_3:_چهرۀ_زمین'],
                                        'children' => [
                                            [
                                                'id' => '1471',
                                                'name' => 'محیط‌های زمین',
                                                'tags' => ['محیط‌های_زمین'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1472',
                                                'name' => 'خشکی‌ها',
                                                'tags' => ['خشکی‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1475',
                                                'name' => 'ناهمواری‌ها',
                                                'tags' => ['ناهمواری‌ها'],
                                                'children' => [
                                                    [
                                                        'id' => '1473',
                                                        'name' => 'عوامل درونی',
                                                        'tags' => ['عوامل_درونی'],

                                                        'children' => [

                                                        ],
                                                    ],
                                                    [
                                                        'id' => '1474',
                                                        'name' => 'عوامل بیرونی',
                                                        'tags' => ['عوامل_بیرونی'],

                                                        'children' => [

                                                        ],
                                                    ],

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1483',
                                        'name' => 'درس 4: آب فراوان، هوای پاک',
                                        'tags' => ['درس_4:_آب_فراوان،_هوای_پاک'],

                                        'children' => [
                                            [
                                                'id' => '1477',
                                                'name' => 'پنج مجموعۀ آبی بزرگ',
                                                'tags' => ['پنج_مجموعۀ_آبی_بزرگ'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1478',
                                                'name' => 'ناهمواری‌های کف اقیانوس‌ها',
                                                'tags' => ['ناهمواری‌های_کف_اقیانوس‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1479',
                                                'name' => 'انسان و اقیانوس‌ها',
                                                'tags' => ['انسان_و_اقیانوس‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1480',
                                                'name' => 'هواکره (اتمسفر)',
                                                'tags' => ['هواکره_(اتمسفر)'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1481',
                                                'name' => 'تنوع آب‌و‌هوا در جهان',
                                                'tags' => ['تنوع_آب‌و‌هوا_در_جهان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1482',
                                                'name' => 'عوامل مؤثر بر آب‌وهوای جهان',
                                                'tags' => ['عوامل_مؤثر_بر_آب‌وهوای_جهان'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1487',
                                        'name' => 'درس 5: پراکندگی زیست‌بوم‌های جهان',
                                        'tags' => ['درس_5:_پراکندگی_زیست‌بوم‌های_جهان'],

                                        'children' => [
                                            [
                                                'id' => '1484',
                                                'name' => 'زیست‌بوم (بیوم)',
                                                'tags' => ['زیست‌بوم_(بیوم)'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1485',
                                                'name' => 'تنوع زیست‌بوم‌ها به چه عواملی بستگی دارد؟',
                                                'tags' => ['تنوع_زیست‌بوم‌ها_به_چه_عواملی_بستگی_دارد؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1486',
                                                'name' => 'پراکندگی زیست‌بوم‌های جهان',
                                                'tags' => ['پراکندگی_زیست‌بوم‌های_جهان'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1491',
                                        'name' => 'درس 6: زیست‌بوم‌ها در خطرند',
                                        'tags' => ['درس_6:_زیست‌بوم‌ها_در_خطرند'],

                                        'children' => [
                                            [
                                                'id' => '1488',
                                                'name' => 'انقراض گونه‌ها',
                                                'tags' => ['انقراض_گونه‌ها'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1489',
                                                'name' => 'زیست‌گاه‌ها چرا و چگونه تخریب می‌شوند؟',
                                                'tags' => ['زیست‌گاه‌ها_چرا_و_چگونه_تخریب_می‌شوند؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1490',
                                                'name' => 'چه باید کرد؟',
                                                'tags' => ['چه_باید_کرد؟'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1497',
                                        'name' => 'درس 7: جمعیت جهان',
                                        'tags' => ['درس_7:_جمعیت_جهان'],
                                        'children' => [
                                            [
                                                'id' => '1492',
                                                'name' => 'تغییر رشد جمعیت در جهان',
                                                'tags' => ['تغییر_رشد_جمعیت_در_جهان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1493',
                                                'name' => 'رشد جمعیت در کشورهای جهان',
                                                'tags' => ['رشد_جمعیت_در_کشورهای_جهان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1494',
                                                'name' => 'پراکندگی جمعیت',
                                                'tags' => ['پراکندگی_جمعیت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1495',
                                                'name' => 'جا‌به‌جایی جمعیت (مهاجرت)',
                                                'tags' => ['جا‌به‌جایی_جمعیت_(مهاجرت)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1496',
                                                'name' => 'افزایش شهر‌نشینی',
                                                'tags' => ['افزایش_شهر‌نشینی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1501',
                                        'name' => 'درس 8: جهان نا‌برابر',
                                        'tags' => ['درس_8:_جهان_نا‌برابر'],
                                        'children' => [
                                            [
                                                'id' => '1498',
                                                'name' => 'نابرابری جهانی یا بین‌المللی و معیارهای آن',
                                                'tags' => ['نابرابری_جهانی_یا_بین‌المللی_و_معیارهای_آن'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1499',
                                                'name' => 'شاخص توسعۀ انسانی چیست؟',
                                                'tags' => ['شاخص_توسعۀ_انسانی_چیست؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1500',
                                                'name' => 'علل و عوامل نابرابری',
                                                'tags' => ['علل_و_عوامل_نابرابری'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1507',
                                        'name' => 'درس 9: ایرانی متحد و یکپارچه',
                                        'tags' => ['درس_9:_ایرانی_متحد_و_یکپارچه'],

                                        'children' => [
                                            [
                                                'id' => '1502',
                                                'name' => 'اوضاع سیاسی ایران هنگام تأسیس صفوی',
                                                'tags' => ['اوضاع_سیاسی_ایران_هنگام_تأسیس_صفوی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1503',
                                                'name' => 'شکل‌گیری حکومت صفویه',
                                                'tags' => ['شکل‌گیری_حکومت_صفویه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1504',
                                                'name' => 'قدرت و سقوط صفویان',
                                                'tags' => ['قدرت_و_سقوط_صفویان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1505',
                                                'name' => 'صفویان چگونه کشور را اداره می‌کردند؟',
                                                'tags' => ['صفویان_چگونه_کشور_را_اداره_می‌کردند؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1506',
                                                'name' => 'اروپاییان در راه ایران',
                                                'tags' => ['اروپاییان_در_راه_ایران'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1512',
                                        'name' => 'درس 10: اوضاع اجتماعی، اقتصادی، علمی و فرهنگی ایران در عصر صفوی',
                                        'tags' => ['درس_10:_اوضاع_اجتماعی،_اقتصادی،_علمی_و_فرهنگی_ایران_در_عصر_صفوی'],

                                        'children' => [
                                            [
                                                'id' => '1508',
                                                'name' => 'زندگی اجتماعی',
                                                'tags' => ['زندگی_اجتماعی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1509',
                                                'name' => 'شکوفایی صنعت',
                                                'tags' => ['شکوفایی_صنعت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1510',
                                                'name' => 'رونق تجارت',
                                                'tags' => ['رونق_تجارت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1511',
                                                'name' => 'شکوفایی علمی و فرهنگی',
                                                'tags' => ['شکوفایی_علمی_و_فرهنگی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1516',
                                        'name' => 'درس 11: تلاش برای حفظ استقلال و اتحاد سیاسی ایران',
                                        'tags' => ['درس_11:_تلاش_برای_حفظ_استقلال_و_اتحاد_سیاسی_ایران'],

                                        'children' => [
                                            [
                                                'id' => '1513',
                                                'name' => 'افشاریه',
                                                'tags' => ['افشاریه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1514',
                                                'name' => 'زندیه',
                                                'tags' => ['زندیه'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1515',
                                                'name' => 'قاجاریه: گسترش نفوذ و دخالت کشورهای استعمارگر',
                                                'tags' => ['قاجاریه:_گسترش_نفوذ_و_دخالت_کشورهای_استعمارگر'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1519',
                                        'name' => 'درس 12: در جست‌و‌جوی پیشرفت و رهایی از سلطۀ خارجی',
                                        'tags' => ['درس_12:_در_جست‌و‌جوی_پیشرفت_و_رهایی_از_سلطۀ_خارجی'],

                                        'children' => [
                                            [
                                                'id' => '1517',
                                                'name' => 'تلاش برای نوسازی و اصلاح امور کشور',
                                                'tags' => ['تلاش_برای_نوسازی_و_اصلاح_امور_کشور'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1518',
                                                'name' => 'مبارزه با نفوذ و سلطۀ اقتصادی بیگانگان',
                                                'tags' => ['مبارزه_با_نفوذ_و_سلطۀ_اقتصادی_بیگانگان'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1524',
                                        'name' => 'درس 13: انقلاب مشروطیت؛ موانع و مشکلات',
                                        'tags' => ['درس_13:_انقلاب_مشروطیت؛_موانع_و_مشکلات'],

                                        'children' => [
                                            [
                                                'id' => '1520',
                                                'name' => 'مشروطه چیست؟',
                                                'tags' => ['مشروطه_چیست؟'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1521',
                                                'name' => 'زمینه‌های انقلاب مشروطه',
                                                'tags' => ['زمینه‌های_انقلاب_مشروطه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1522',
                                                'name' => 'انقلاب مشروطه چگونه رخ داد؟',
                                                'tags' => ['انقلاب_مشروطه_چگونه_رخ_داد؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1523',
                                                'name' => 'موانع و مشکلات حکومت مشروطه',
                                                'tags' => ['موانع_و_مشکلات_حکومت_مشروطه'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1530',
                                        'name' => 'درس 14: ایران در دوران حکومت پهلوی',
                                        'tags' => ['درس_14:_ایران_در_دوران_حکومت_پهلوی'],

                                        'children' => [
                                            [
                                                'id' => '1525',
                                                'name' => 'زمینه‌های تغییر حکومت از قاجاریه به پهلوی',
                                                'tags' => ['زمینه‌های_تغییر_حکومت_از_قاجاریه_به_پهلوی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1526',
                                                'name' => 'شیوۀ حکومت و اقدامات رضاشاه',
                                                'tags' => ['شیوۀ_حکومت_و_اقدامات_رضاشاه'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1527',
                                                'name' => 'آثار جنگ‌ جهانی دوم بر ایران',
                                                'tags' => ['آثار_جنگ‌_جهانی_دوم_بر_ایران'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1528',
                                                'name' => 'نهضت ملی شدن نفت',
                                                'tags' => ['نهضت_ملی_شدن_نفت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1529',
                                                'name' => 'کودتای 28 مرداد 1332',
                                                'tags' => ['کودتای_28_مرداد_1332'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1534',
                                        'name' => 'درس 15: انقلاب اسلامی ایران',
                                        'tags' => ['درس_15:_انقلاب_اسلامی_ایران'],

                                        'children' => [
                                            [
                                                'id' => '1531',
                                                'name' => 'نهضت اسلامی به رهبری امام خمینی (ره)',
                                                'tags' => ['نهضت_اسلامی_به_رهبری_امام_خمینی_(ره)'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1532',
                                                'name' => 'اوضاع ایران در آستانۀ انقلاب اسلامی',
                                                'tags' => ['اوضاع_ایران_در_آستانۀ_انقلاب_اسلامی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1533',
                                                'name' => 'انقلاب اسلامی از آغاز تا پیروزی',
                                                'tags' => ['انقلاب_اسلامی_از_آغاز_تا_پیروزی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1538',
                                        'name' => 'درس 16: ایران در دوران پس از پیروزی انقلاب اسلامی',
                                        'tags' => ['درس_16:_ایران_در_دوران_پس_از_پیروزی_انقلاب_اسلامی'],

                                        'children' => [
                                            [
                                                'id' => '1535',
                                                'name' => 'برپایی نظام جمهوری اسلامی',
                                                'tags' => ['برپایی_نظام_جمهوری_اسلامی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1536',
                                                'name' => 'توطئه‌ها و دسیسه‌های دشمنان',
                                                'tags' => ['توطئه‌ها_و_دسیسه‌های_دشمنان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1537',
                                                'name' => 'رحلت امام‌خمینی (ره) و انتخاب حضرت آیت‌الله خامنه‌ای به رهبری',
                                                'tags' => ['رحلت_امام‌خمینی_(ره)_و_انتخاب_حضرت_آیت‌الله_خامنه‌ای_به_رهبری'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1542',
                                        'name' => 'درس 17: فرهنگ',
                                        'tags' => ['درس_17:_فرهنگ'],
                                        'children' => [
                                            [
                                                'id' => '1539',
                                                'name' => 'فرهنگ، شیوۀ زندگی',
                                                'tags' => ['فرهنگ،_شیوۀ_زندگی'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1540',
                                                'name' => 'فرهنگ آموختنی است',
                                                'tags' => ['فرهنگ_آموختنی_است'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1541',
                                                'name' => 'لایه‌های فرهنگ',
                                                'tags' => ['لایه‌های_فرهنگ'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1547',
                                        'name' => 'درس 18: هویت',
                                        'tags' => ['درس_18:_هویت'],
                                        'children' => [
                                            [
                                                'id' => '1543',
                                                'name' => 'منظور از هویت چیست؟',
                                                'tags' => ['منظور_از_هویت_چیست؟'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1544',
                                                'name' => 'ابعاد فردی و اجتماعی هویت',
                                                'tags' => ['ابعاد_فردی_و_اجتماعی_هویت'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1545',
                                                'name' => 'ویژگی‌های هویتی ما (انتسابی و اکتسابی) و تغییر آن‌ها',
                                                'tags' => ['ویژگی‌های_هویتی_ما_(انتسابی_و_اکتسابی)_و_تغییر_آن‌ها'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1546',
                                                'name' => 'هویت ملی و هویت ایرانی',
                                                'tags' => ['هویت_ملی_و_هویت_ایرانی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1548',
                                        'name' => 'درس 19: کارکرد‌های خانواده',
                                        'tags' => ['درس_19:_کارکرد‌های_خانواده'],

                                        'children' => [

                                        ],
                                    ],
                                    [
                                        'id' => '1553',
                                        'name' => 'درس 20: آرامش در خانواده',
                                        'tags' => ['درس_20:_آرامش_در_خانواده'],
                                        'children' => [
                                            [
                                                'id' => '1549',
                                                'name' => 'همسر گزینی',
                                                'tags' => ['همسر_گزینی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1550',
                                                'name' => 'سازگاری زن و شوهر در خانواده',
                                                'tags' => ['سازگاری_زن_و_شوهر_در_خانواده'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1551',
                                                'name' => 'سازگاری والدین و فرزندان',
                                                'tags' => ['سازگاری_والدین_و_فرزندان'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1552',
                                                'name' => 'مدیریت مشکلات و حوادث در خانواده',
                                                'tags' => ['مدیریت_مشکلات_و_حوادث_در_خانواده'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1558',
                                        'name' => 'درس 21: نهاد حکومت',
                                        'tags' => ['درس_21:_نهاد_حکومت'],
                                        'children' => [
                                            [
                                                'id' => '1554',
                                                'name' => 'نیاز به حکومت',
                                                'tags' => ['نیاز_به_حکومت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1555',
                                                'name' => 'وظایف حکومت',
                                                'tags' => ['وظایف_حکومت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1556',
                                                'name' => 'اسلام و حکومت',
                                                'tags' => ['اسلام_و_حکومت'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1557',
                                                'name' => 'حکومت در کشور ما',
                                                'tags' => ['حکومت_در_کشور_ما'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1562',
                                        'name' => 'درس 22: حقوق و تکالیف شهروندی',
                                        'tags' => ['درس_22:_حقوق_و_تکالیف_شهروندی'],

                                        'children' => [
                                            [
                                                'id' => '1559',
                                                'name' => 'شهروندی',
                                                'tags' => ['شهروندی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1560',
                                                'name' => 'حقوق شهروندی',
                                                'tags' => ['حقوق_شهروندی'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1561',
                                                'name' => 'تکالیف شهروندی',
                                                'tags' => ['تکالیف_شهروندی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1567',
                                        'name' => 'درس 23: بهره‌وری چیست؟',
                                        'tags' => ['درس_23:_بهره‌وری_چیست؟'],
                                        'children' => [
                                            [
                                                'id' => '1563',
                                                'name' => 'بررسی موقعیت‌هایی درباره بهره‌وری',
                                                'tags' => ['بررسی_موقعیت‌هایی_درباره_بهره‌وری'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1564',
                                                'name' => 'بهره‌وری چیست؟',
                                                'tags' => ['بهره‌وری_چیست؟'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1565',
                                                'name' => 'فرهنگ بهره‌وری',
                                                'tags' => ['فرهنگ_بهره‌وری'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1566',
                                                'name' => 'بهره‌وری در زندگی فردی و خانوادگی',
                                                'tags' => ['بهره‌وری_در_زندگی_فردی_و_خانوادگی'],

                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1573',
                                        'name' => 'درس 24: اقتصاد و بهره‌وری',
                                        'tags' => ['درس_24:_اقتصاد_و_بهره‌وری'],

                                        'children' => [
                                            [
                                                'id' => '1568',
                                                'name' => 'نهاد اقتصاد',
                                                'tags' => ['نهاد_اقتصاد'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1569',
                                                'name' => 'بهره‌وری در تولید، توزیع و مصرف',
                                                'tags' => ['بهره‌وری_در_تولید،_توزیع_و_مصرف'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1570',
                                                'name' => 'بهره‌وری سبز',
                                                'tags' => ['بهره‌وری_سبز'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1571',
                                                'name' => 'بهره‌وری در کشور ما',
                                                'tags' => ['بهره‌وری_در_کشور_ما'],

                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1572',
                                                'name' => 'اقتصاد مقاومتی',
                                                'tags' => ['اقتصاد_مقاومتی'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],
                            [
                                'id' => '1611',
                                'name' => 'پیام‌های آسمان',
                                'tags' => ['پیام‌های_آسمان'],
                                'children' => [
                                    [
                                        'id' => '1577',
                                        'name' => 'درس اول: تو را چگونه بشناسم؟',
                                        'tags' => ['درس_اول:_تو_را_چگونه_بشناسم؟'],

                                        'children' => [
                                            [
                                                'id' => '1575',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1576',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1580',
                                        'name' => 'درس دوم: در پناه ایمان',
                                        'tags' => ['درس_دوم:_در_پناه_ایمان'],
                                        'children' => [
                                            [
                                                'id' => '1578',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1579',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1583',
                                        'name' => 'درس سوم: راهنمایان الهی',
                                        'tags' => ['درس_سوم:_راهنمایان_الهی'],
                                        'children' => [
                                            [
                                                'id' => '1581',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1582',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1586',
                                        'name' => 'درس چهارم: خورشید پنهان',
                                        'tags' => ['درس_چهارم:_خورشید_پنهان'],
                                        'children' => [
                                            [
                                                'id' => '1584',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1585',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1589',
                                        'name' => 'درس پنجم: رهبری در دوران غیبت',
                                        'tags' => ['درس_پنجم:_رهبری_در_دوران_غیبت'],

                                        'children' => [
                                            [
                                                'id' => '1587',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1588',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1592',
                                        'name' => 'درس ششم: وضو، غسل و تیمم',
                                        'tags' => ['درس_ششم:_وضو،_غسل_و_تیمم'],
                                        'children' => [
                                            [
                                                'id' => '1590',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1591',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1595',
                                        'name' => 'درس هفتم: احکام نماز',
                                        'tags' => ['درس_هفتم:_احکام_نماز'],
                                        'children' => [
                                            [
                                                'id' => '1593',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1594',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1598',
                                        'name' => 'درس هشتم: همدلی و همراهی',
                                        'tags' => ['درس_هشتم:_همدلی_و_همراهی'],
                                        'children' => [
                                            [
                                                'id' => '1596',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1597',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1601',
                                        'name' => 'درس نهم: انقلاب اسلامی ایران',
                                        'tags' => ['درس_نهم:_انقلاب_اسلامی_ایران'],

                                        'children' => [
                                            [
                                                'id' => '1599',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1600',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1604',
                                        'name' => 'درس دهم: مسئولیت همگانی',
                                        'tags' => ['درس_دهم:_مسئولیت_همگانی'],
                                        'children' => [
                                            [
                                                'id' => '1602',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1603',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1607',
                                        'name' => 'درس یازدهم: انفاق',
                                        'tags' => ['درس_یازدهم:_انفاق'],
                                        'children' => [
                                            [
                                                'id' => '1605',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1606',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],
                                    [
                                        'id' => '1610',
                                        'name' => 'درس دوازدهم: جهاد',
                                        'tags' => ['درس_دوازدهم:_جهاد'],
                                        'children' => [
                                            [
                                                'id' => '1608',
                                                'name' => 'آیات و روایات',
                                                'tags' => ['آیات_و_روایات'],
                                                'children' => [

                                                ],
                                            ],
                                            [
                                                'id' => '1609',
                                                'name' => 'متن',
                                                'tags' => ['متن'],
                                                'children' => [

                                                ],
                                            ],

                                        ],
                                    ],

                                ],
                            ],

                        ],
                    ],
                ],
            ],
            [
                'name' => 'متوسطه2',
                'tags' => ['متوسطه2'],
                'children' => $reshteh,
            ],
            [
                'name' => 'مهارتی',
                'tags' => ['مهارتی'],
                'enable' => false,
                'children' => [],
            ],

        ];
        $alaa = [
            'name' => 'آلاء',
            'children' => $paye,
        ];

        return $alaa;
    }
}
