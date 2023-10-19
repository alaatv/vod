<?php

namespace App\Console\Commands;

use App\Exports\DefaultClassExport;
use App\Models\Content;
use App\Models\Coupon;
use App\Models\Gender;
use App\Models\Major;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\OrderRepo;
use App\Traits\DateTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class
GenerateExcelReportCommandOld extends Command
{
    use DateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'alaaTv:generateExcelReportOld {action : Choosing the action : customers , orderproducts , transactions , contents , customersInfo , users , raheAbrishamCustomers , dailySaleReport , monthlyContentsHours , monthlySaleReport , registerToPurchasePeriod, general} , {mobile?} , {--from= : get report from this date} , {--to= : get report to this date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export sales report on excel file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $action = $this->argument('action');
        $mobile = $this->argument('mobile');
        $headers = [
            'جنسیت', 'فروردین 98', 'اردیبهشت 98', 'خرداد 98', 'تیر 98', 'مرداد 98', 'شهریور 98', 'مهر 98', 'آبان 98',
            'آذر 98', 'دی 98', 'بهمن 98', 'اسفند 98', 'فروردین 99', 'اردیبهشت 99', 'خرداد 99', 'تیر 99', 'مرداد 99',
            'شهریور 99', 'مهر 99', 'آبان 99', 'آذر 99'
        ];

        // TODO: notif user by this mobile by emad style
        switch ($action) {
            case 'customers':
                $table = $this->generateTableOfCustomers();
                break;
            case 'orderproducts':
                $table = $this->generateTableOfOrderproducts();
                break;
            case 'transactions':
                $table = $this->generateTableOfIncome();
                break;
            case 'contents':
                $table = $this->generateTableOfContentsCount();
                $headers = ['سال', 'تعداد جزوات', 'تعداد فیلم ها'];
                break;
            case 'customersInfo':
                $table = $this->generateTableOfProductCustomersinfo();
                $headers = ['نام', 'شماره تماس', 'جزوه شیمی پویان نظر', 'جزوه فیزیک طلوعی'];
                break;
            case 'users':
                $table = $this->generateTableOfRegisteredUsers();
                break;
            case 'raheAbrishamCustomers':
                $table = $this->generateTableOfRaheAbrishamCustomers();
                $headers = [
                    'نام', 'موبایل', 'رشته', 'شهر', 'استان', 'تعداد تیکت', 'ریاضیات ریاضی', 'فیزیک ریاضی',
                    'فیزیک تجربی', 'زیست', 'شیمی', 'ریاضی تجربی'
                ];
                break;
            case 'dailySaleReport':
                $table = $this->generateTableOfDailySaleReport();
                $headers = [
                    ' ', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17',
                    '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'
                ];
                break;
            case 'monthlyContentsHours':
                $table = $this->generateTableOfMonthlyContentsCount();
                $headers = ['ماه', 'جمع ساعت فیلم های رایگان', 'جمع ساعت فیلم های پولی'];
                break;
            case 'monthlySaleReport':
                $table = $this->generateTableOfMonthlySaleReport();
                $headers = [
                    'آذر 99', 'دی 99', 'بهمن 99', 'اسفند 99', 'فروردین 1400', 'اردیبهشت 1400', 'خرداد 1400', 'تیر 1400',
                    'مرداد 1400', 'شهریور 1400'
                ];
                break;
            case 'coupons1400':
                $table = $this->generateTableOfCoupons1400();
                $headers = ['کد', 'نوع', 'درصد', 'تاریخ ایجاد', 'مهلت استفاده', 'تعداد استفاده',];
                break;
            case 'orderProducts1400':
                $table = $this->generateTableOfOrderProducts1400();
                $headers = ['شناسه', 'نام', 'درصد تخفیف', 'قیمت تمام شده', 'حذف شده', 'تاریخ افزودن به سبد',];
                break;
            case 'paidOrders1400':
                $table = $this->generateTableOfPaidOrders1400();
                $headers = [
                    'شناسه', 'قیمت نهایی (پرداخت کرده)', 'قیمت نهایی تمام شده', 'تخفیف کلی', 'وضعیت پرداخت', 'کد تخفیف',
                    'درصد کد تخفیف', 'جمع تراکنش های واقعی', 'جمع کیف پول', 'تاریخ ثبت', 'تاریخ اولین پرداخت',
                ];
                break;
            case 'general':
                $columns = ['mobile' => 'شماره تماس'];
                $table = $this->general($columns);
                $headers = array_values($columns);
                break;

            case 'registerToPurchasePeriod':
                // if from and to option not set it report from 10 years ago till now
                $timeZone = 'Asia/Tehran';
                $from = $this->option('from') ?? now()->subYears(10)->setTimezone($timeZone);
                $to = $this->option('to') ?? now()->setTimezone($timeZone);
                $reports = $this->registerToPurchasePeriodReport($from, $to);
                $this->info("\n--------------------------REPORTS--------------------------\n");
                foreach ($reports as $title => $report) {
                    $day = ($report > 1) ? 'days' : 'day';
                    $this->info(Str::upper($title)." = $report ".$day."\n");
                }
                break;
            default:
                break;
        }

        if (!isset($table)) {
            // TODO: notif user by this mobile by emad style
            // $user()->notify(new ReportNotCreatedNotification());
            return null;
        }
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_{$action}_{$now}.xlsx";
        $disk = config('disks.GENERAL');
        try {
            Excel::store(new DefaultClassExport($table, $headers), $fileName, $disk);
            // TODO: notif user by this mobile by emad style
            // $user()->notify(new ReportCreatedNotification(route('web.download', ['content' => $disk, 'fileName' => $fileName])));
        } catch (Exception $exception) {
            // TODO: notif user by this mobile by emad style
            // $user()->notify(new ReportNotCreatedNotification());
        }

        $this->info('Done');
        return 0;
    }

    private function generateTableOfCustomers(): Collection
    {
        $rows = $this->getRows();
        $bar = $this->output->createProgressBar($rows->count());
        $table = collect();
        foreach ($rows as $key => $row) {
            $this->info('Processing row '.$key);
            $rowCollection = collect();
            $columns = $this->get98Months();
            foreach ($columns as $timePeriod) {
                $this->info('Processing column '.$timePeriod['month']);
                $users = User::whereHas('orders', function ($q) use ($timePeriod) {
                    $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                        ->whereIn('paymentstatus_id', [
                            config('constants.PAYMENT_STATUS_PAID'),
                            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                        ])
                        ->whereHas('transactions', function ($q2) use ($timePeriod) {
                            $q2->where('cost', '>', 0)
                                ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                                ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'));
                        })->where('completed_at', '>=', $timePeriod['periodBegin'].' 00:00:00')
                        ->where('completed_at', '<', $timePeriod['periodEnd'].' 00:00:00');
                })->get();

                if (isset($row['major_id'])) {
                    $users = $users->where('major_id', $row['major_id']);
                } else {
                    $users = $users->whereNull('major_id');
                }

                if (isset($row['gender_id'])) {
                    $users = $users->where('gender_id', $row['gender_id']);
                } else {
                    $users = $users->whereNull('gender_id');
                }

                $columnData = $users->count();

                $rowCollection->push($columnData);
            }

            $rowCollection->prepend($key);
            $table->put($key, $rowCollection);
            $bar->advance();

        }

        $bar->finish();
        return $table;
    }

    private function getRows(): Collection
    {
        return collect([
            'رشته ریاضی دختر' => ['major_id' => Major::RIYAZI, 'gender_id' => Gender::GIRL],
            'رشته ریاضی پسر' => ['major_id' => Major::RIYAZI, 'gender_id' => Gender::BOY],
            'رشته ریاضی نامشخص' => ['major_id' => Major::RIYAZI, 'gender_id' => null],
            'تجربی دختر' => ['major_id' => Major::TAJROBI, 'gender_id' => Gender::GIRL],
            'تجربی پسر' => ['major_id' => Major::TAJROBI, 'gender_id' => Gender::BOY],
            'تجربی نامشخص' => ['major_id' => Major::TAJROBI, 'gender_id' => null],
            'انسانی دختر' => ['major_id' => Major::ENSANI, 'gender_id' => Gender::GIRL],
            'انسانی پسر' => ['major_id' => Major::ENSANI, 'gender_id' => Gender::BOY],
            'انسانی نامشخص' => ['major_id' => Major::ENSANI, 'gender_id' => null],
            'نامشخص دختر' => ['major_id' => null, 'gender_id' => Gender::GIRL],
            'نامشخص پسر' => ['major_id' => null, 'gender_id' => Gender::BOY],
            'نامشخص نامشخص' => ['major_id' => null, 'gender_id' => null],
        ]);
    }

    private function get98Months(): Collection
    {
        return collect([
            [
                'month' => 'فروردین 98',
                'periodBegin' => '2019-03-21',
                'periodEnd' => '2019-04-21',
            ],
            [
                'month' => 'اردیبهشت 98',
                'periodBegin' => '2019-04-21',
                'periodEnd' => '2019-05-22',
            ],
            [
                'month' => 'خرداد 98',
                'periodBegin' => '2019-05-22',
                'periodEnd' => '2019-06-22',
            ],
            [
                'month' => 'تیر 98',
                'periodBegin' => '2019-06-22',
                'periodEnd' => '2019-07-23',
            ],
            [
                'month' => 'مرداد 98',
                'periodBegin' => '2019-07-23',
                'periodEnd' => '2019-08-23',
            ],
            [
                'month' => 'شهریور 98',
                'periodBegin' => '2019-08-23',
                'periodEnd' => '2019-09-23',
            ],
            [
                'month' => 'مهر 98',
                'periodBegin' => '2019-09-23',
                'periodEnd' => '2019-10-23',
            ],
            [
                'month' => 'آبان 98',
                'periodBegin' => '2019-10-23',
                'periodEnd' => '2019-11-22',
            ],
            [
                'month' => 'آذر 98',
                'periodBegin' => '2019-11-22',
                'periodEnd' => '2019-12-22',
            ],
            [
                'month' => 'دی 98',
                'periodBegin' => '2019-12-22',
                'periodEnd' => '2020-01-21',
            ],
            [
                'month' => 'بهمن 98',
                'periodBegin' => '2020-01-21',
                'periodEnd' => '2020-02-20',
            ],
            [
                'month' => 'اسفند 98',
                'periodBegin' => '2020-02-20',
                'periodEnd' => '2020-03-20',
            ],
        ]);
    }

    private function generateTableOfOrderproducts(): Collection
    {
        $rows = $this->getRows();
        $table = collect();
        $bar = $this->output->createProgressBar($rows->count());
        foreach ($rows as $key => $row) {
            $this->info('Processing row '.$key);
            $rowCollection = collect();
            $columns = $this->get98Months();
            foreach ($columns as $timePeriod) {
                $this->info('Processing column '.$timePeriod['month']);
                $orderproducts = Orderproduct::query()
                    ->where(function ($q1) {
                        $q1->where('tmp_share_order', '>', 0)->orWhereIn('product_id', [180, 182]);
                    })
                    ->whereHas('order', function ($q2) use ($timePeriod, $row) {
                        $q2->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                            ->whereIn('paymentstatus_id', [
                                config('constants.PAYMENT_STATUS_PAID'),
                                config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                            ])
                            ->whereHas('transactions', function ($q2) use ($timePeriod) {
                                $q2
                                    ->where('cost', '>', 0)->where('transactionstatus_id',
                                        config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                                    ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'));
                            })->where('completed_at', '>=', $timePeriod['periodBegin'].' 00:00:00')
                            ->where('completed_at', '<', $timePeriod['periodEnd'].' 00:00:00')
                            ->whereHas('user', function ($q3) use ($row) {
                                if (isset($row['major_id'])) {
                                    $q3->where('major_id', $row['major_id']);
                                } else {
                                    $q3->whereNull('major_id');
                                }

                                if (isset($row['gender_id'])) {
                                    $q3->where('gender_id', $row['gender_id']);
                                } else {
                                    $q3->whereNull('gender_id');
                                }
                            });
                    });

                $columnData = $orderproducts->count();

                $rowCollection->push($columnData);
            }

            $rowCollection->prepend($key);
            $table->put($key, $rowCollection);

            $bar->advance();

        }

        $bar->finish();
        return $table;
    }

    private function generateTableOfIncome(): Collection
    {
        $rows = $this->getRows();

        $bar = $this->output->createProgressBar($rows->count());
        $table = collect();
        foreach ($rows as $key => $row) {
            $this->info('Processing row '.$key);
            $rowCollection = collect();
            $columns = $this->get98Months();
            foreach ($columns as $timePeriod) {
                $this->info('Processing column '.$timePeriod['month']);
                $transactions = Transaction::query()
                    ->whereHas('order', function ($q) use ($row) {
                        $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                            ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
                            ->whereHas('user', function ($q3) use ($row) {
                                if (isset($row['major_id'])) {
                                    $q3->where('major_id', $row['major_id']);
                                } else {
                                    $q3->whereNull('major_id');
                                }

                                if (isset($row['gender_id'])) {
                                    $q3->where('gender_id', $row['gender_id']);
                                } else {
                                    $q3->whereNull('gender_id');
                                }
                            });
                    })
                    ->where('completed_at', '>=', $timePeriod['periodBegin'].' 00:00:00')->where('completed_at', '<',
                        $timePeriod['periodEnd'].' 00:00:00')
                    ->whereNull('wallet_id')
                    ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                    ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                    ->get();
                $totalSale = $transactions->sum('cost');
//                $checkedOrderproducts = [];
//                /** @var \App\Transaction $transaction */
//                foreach ($transactions as $key => $transaction)
//                {
//                    $order = $transaction->order;
//
//                    if(!isset($order))
//                    {
//                        continue;
//                    }
//
//                    $orderproducts = $order->orderproducts ;
//                    if(!isset($orderproducts))
//                    {
//                        continue;
//                    }
//
//                    foreach ($orderproducts as $orderproduct)
//                    {
//                        if(in_array($orderproduct->id, $checkedOrderproducts))
//                        {
//                            continue;
//                        }
//
//                        $checkedOrderproducts[] = $orderproduct->id;
//
//                        if(in_array($orderproduct->product_id, [Product::CUSTOM_DONATE_PRODUCT , Product::DONATE_PRODUCT_5_HEZAR]))
//                        {
//                            $totalSale += $orderproduct->cost;
//                            continue;
//                        }
//
//                        $orderproductCost = (int)ceil($orderproduct->getSharedCostOfTransaction());
//                        $totalSale += $orderproductCost;
//                    }
//                }

                $rowCollection->push(number_format($totalSale));
            }

            $rowCollection->prepend($key);
            $table->put($key, $rowCollection);
            $bar->advance();

        }

        $bar->finish();

        return $table;
    }

    private function generateTableOfContentsCount(): Collection
    {
        $rows = $this->getYears();
        $bar = $this->output->createProgressBar($rows->count());
        $table = collect();
        foreach ($rows as $row) {
            $this->info('Processing row '.$row['year']);
            $rowCollection = collect();
            $contents = Content::enable()->whereNull('redirectUrl')->whereBetween('created_at',
                [$row['periodBegin'], $row['periodEnd']])->get();

            $pamphlets = $contents->where('contenttype_id', Content::CONTENT_TYPE_PAMPHLET)->count();
            $videos = $contents->where('contenttype_id', Content::CONTENT_TYPE_VIDEO)->count();

            $rowCollection->push([$row['year'], $pamphlets ?? 0, $videos ?? 0]);

            $table->push($rowCollection);
            $bar->advance();

        }

        $bar->finish();
        $this->info('Done');
        return $table;
    }

    private function getYears(): Collection
    {
        return collect([
            [
                'year' => '1392',
                'periodBegin' => '2013-03-21',
                'periodEnd' => '2014-03-21',
            ],
            [
                'year' => '1393',
                'periodBegin' => '2014-03-21',
                'periodEnd' => '2015-03-21',
            ],
            [
                'year' => '1394',
                'periodBegin' => '2015-03-21',
                'periodEnd' => '2016-03-20',
            ],
            [
                'year' => '1395',
                'periodBegin' => '2016-03-20',
                'periodEnd' => '2017-03-21',
            ],
            [
                'year' => '1396',
                'periodBegin' => '2017-03-21',
                'periodEnd' => '2018-03-21',
            ],
            [
                'year' => '1397',
                'periodBegin' => '2018-03-21',
                'periodEnd' => '2019-03-21',
            ],
            [
                'year' => '1398',
                'periodBegin' => '2019-03-21',
                'periodEnd' => '2020-03-20',
            ],
            [
                'year' => '1399',
                'periodBegin' => '2020-03-20',
                'periodEnd' => '2021-03-21',
            ],
        ]);
    }

    private function generateTableOfProductCustomersinfo()
    {
        $productIds = [466, 464];

        $users = User::whereHas('orders', function ($q) use ($productIds) {
            $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                ->whereIn('paymentstatus_id',
                    [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
                ->whereHas('transactions', function ($q2) use ($productIds) {
                    $q2->where('cost', '>', 0)
                        ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                        ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'));
                })
                ->whereHas('orderproducts', function ($q2) use ($productIds) {
                    $q2->whereIn('product_id', $productIds);
                });
        })->get();

        $usersCount = $users->count();

        if (!$this->confirm("$usersCount users found. Do you wish to continue?", true)) {
            return null;
        }

        $table = collect();
        /** @var User $user */
        foreach ($users as $user) {
            $columnsOfThisRow = [$user->full_name, $user->mobile];
            foreach ($productIds as $productId) {
                $orderproduct = $user->getPurchasedOrderproduct($productId);
                if (isset($orderproduct)) {
                    array_push($columnsOfThisRow, 1);
                    continue;
                }

                array_push($columnsOfThisRow, 0);
            }


            $table->push(collect($columnsOfThisRow));
        }

        return $table;
    }

    private function generateTableOfRegisteredUsers()
    {
        $rows = $this->getRows();

        $bar = $this->output->createProgressBar($rows->count());
        $table = collect();
        foreach ($rows as $key => $row) {
            $this->info('Processing row '.$key);
            $rowCollection = collect();
            $columns = $this->get98Months();
            foreach ($columns as $timePeriod) {
                $this->info('Processing column '.$timePeriod['month']);
                $users = User::query()->where('created_at', '<=', $timePeriod['periodEnd']);

                if (isset($row['major_id'])) {
                    $users->where('major_id', $row['major_id']);
                } else {
                    $users->whereNull('major_id');
                }

                if (isset($row['gender_id'])) {
                    $users->where('gender_id', $row['gender_id']);
                } else {
                    $users->whereNull('gender_id');
                }

                $columnData = $users->count();

                $rowCollection->push($columnData);
            }

            $rowCollection->prepend($key);
            $table->put($key, $rowCollection);
            $bar->advance();

        }

        $bar->finish();
        return $table;
    }

    private function generateTableOfRaheAbrishamCustomers()
    {
        $since = '2020-08-22 00:00:00';
        $products = Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS;
        $users = User::query()
            ->whereHas('orders', function ($q) use ($products, $since) {
                $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                    ->whereIn('paymentstatus_id',
                        [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
                    ->where('completed_at', '>=', $since)
                    ->whereHas('orderproducts', function ($q2) use ($products) {
                        $q2->whereIn('product_id', $products);
                    })
                    ->whereDoesntHave('user', function ($q3) {
                        $q3->whereHas('roles', function ($q4) {
                            $q4->whereIn('name', [config('constants.ROLE_EMPLOYEE'), 'walletGiver', 'admin']);
                        });
                    });
            })->get();

        $this->info('Making the report');
        $bar = $this->output->createProgressBar($users->count());
        $table = collect();
        /** @var User $user */
        foreach ($users as $key => $user) {
            $countPurchasedRaheAbrisham = $user->countPurchasedProducts(Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS,
                $since);
            if ($countPurchasedRaheAbrisham < 3) {
                continue;
            }

            $ticketsCount = $user->tickets->count();


//            $columnsOfThisRow = [
//                $user->full_name ,
//                $user->mobile,
//                ($user->mojor_id == 1)?'ریاضی': ($user->mojor_id == 2)?'تجربی':($user->mojor_id == 2)?'انسانی':'',
//                (isset($user->city))?$user->city:'',
//                (isset($user->province))?$user->province:'',
//                $ticketsCount,
//
            $major = '';
            if ($user->major_id == 1) {
                $major = 'ریاضی';

            } elseif ($user->major_id == 2) {
                $major = 'تجربی';

            } elseif ($user->major_id == 3) {
                $major = 'انسانی';
            }

            $columnsOfThisRow = [
                $user->full_name,
                $user->mobile,
                $major,
                (isset($user->city)) ? $user->city : '',
                (isset($user->province)) ? $user->province : '',
                $ticketsCount,
            ];


            foreach (Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS as $productId) {
                $orderproduct = $user->orderproducts()
                    ->whereHas('order', function ($q) use ($since) {
                        $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                            ->whereIn('paymentstatus_id', [
                                config('constants.PAYMENT_STATUS_PAID'),
                                config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                            ])
                            ->where('completed_at', '>=', $since);
                    })
                    ->where('product_id', $productId)->first();

                if (!isset($orderproduct)) {

                    array_push($columnsOfThisRow, 0);

                    continue;
                }
                $isParcham = optional($orderproduct->order)->coupon_id == 8526;

                if ($isParcham) {
                    array_push($columnsOfThisRow, 'p');
                    continue;
                }

                array_push($columnsOfThisRow, 1);
                continue;
            }

            $table->put($key, $columnsOfThisRow);
            $bar->advance();
        }

        $bar->finish();
        return $table;
    }

    private function generateTableOfDailySaleReport()
    {
        $rows =
            [
                'number_of_customers' => 'تعداد خریداران', 'number_of_customers_incremental' => 'تجمعی تعداد خریداران',
                'number_of_orderproducts' => 'تعداد فروش', 'total_of_transactions' => 'جمع ورودی(تومان)',
                'total_of_instalments' => 'جمع قسط ها(تومان)', 'total_of_base_price' => 'جمع قیمت بدون تخفیف(تومان)'
            ];

        $table = collect();

        $this->info('Making the report ...');
        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();
        //Month
        $columns = $this->getShahrivar1402();

        foreach ($rows as $key => $row) {
            $this->info('Processing row '.$key);
            $rowCollection = collect();
            foreach ($columns as $key2 => $timePeriod) {
                if ($key2 == 0) {
                    $firstDay = $timePeriod['date'];
                }

                $this->info('Processing day '.$timePeriod['day']);
                switch ($key) {
                    case 'number_of_customers' :
                        $records = User::whereHas('orders', function ($q) use ($timePeriod) {
                            $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                ->whereIn('paymentstatus_id', [
                                    config('constants.PAYMENT_STATUS_PAID'),
                                    config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                                ])
//                                ->whereHas('transactions' , function ($q2)
//                                {
//                                    $q2->where('cost' , '>' , 0)
//                                        ->where('transactionstatus_id' , config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
//                                        ->where('paymentmethod_id' , '<>' , config('constants.PAYMENT_METHOD_WALLET'));
//                                })
                                ->where('completed_at', '>=', $timePeriod['date'].' 00:00:00')
                                ->where('completed_at', '<=', $timePeriod['date'].' 23:59:59');
                        })->get();
                        $columnData = $records->count();
                        break;
                    case 'number_of_customers_incremental' :
                        $records = User::whereHas('orders', function ($q) use ($timePeriod, $firstDay) {
                            $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                ->whereIn('paymentstatus_id', [
                                    config('constants.PAYMENT_STATUS_PAID'),
                                    config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                                ])
//                                ->whereHas('transactions' , function ($q2)
//                                {
//                                    $q2->where('cost' , '>' , 0)
//                                        ->where('transactionstatus_id' , config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
//                                        ->where('paymentmethod_id' , '<>' , config('constants.PAYMENT_METHOD_WALLET'));
//                                })
                                ->where('completed_at', '>=', $firstDay.' 00:00:00')
                                ->where('completed_at', '<=', $timePeriod['date'].' 23:59:59');
                        })->get();
                        $columnData = $records->count();
                        break;
                    case 'number_of_orderproducts' :
                        $records = $orderproducts = Orderproduct::query()
//                            ->where(function ($q1)
//                            {
//                                $q1->where('tmp_share_order' , '>' , 0)->orWhereIn('product_id' , [180 , 182]) ;
//                            })
                            ->whereHas('order', function ($q2) use ($timePeriod, $row) {
                                $q2->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                    ->whereIn('paymentstatus_id', [
                                        config('constants.PAYMENT_STATUS_PAID'),
                                        config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                                    ])
//                                    ->whereHas('transactions' , function ($q2)
//                                    {
//                                        $q2->where('cost' , '>' , 0)->where('transactionstatus_id' , config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
//                                            ->where('paymentmethod_id' , '<>' , config('constants.PAYMENT_METHOD_WALLET'));
//                                    })
                                    ->where('completed_at', '>=', $timePeriod['date'].' 00:00:00')
                                    ->where('completed_at', '<=', $timePeriod['date'].' 23:59:59');
                            });
                        $columnData = $records->count();
                        break;
                    case 'total_of_transactions' :
                        $records = Transaction::query()
                            ->whereHas('order', function ($q) use ($row) {
                                $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                    ->whereIn('paymentstatus_id', [config('constants.PAYMENT_STATUS_PAID')]);
                            })
                            ->where('completed_at', '>=', $timePeriod['date'].' 00:00:00')->where('completed_at', '<=',
                                $timePeriod['date'].' 23:59:59')
                            ->whereNull('wallet_id')
                            ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'))
                            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                            ->get();
                        $columnData = $records->sum('cost');
                        break;
                    case 'total_of_instalments' :
                        $records = Transaction::query()
                            ->whereHas('order', function ($q) use ($row) {
                                $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                    ->whereIn('paymentstatus_id',
                                        [config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')]);
                            })
                            ->where('completed_at', '>=', $timePeriod['date'].' 00:00:00')->where('completed_at', '<=',
                                $timePeriod['date'].' 23:59:59')
                            ->whereNull('wallet_id')
                            ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'))
                            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                            ->get();
                        $columnData = $records->sum('cost');
                        break;
                    case 'total_of_base_price' :
                        $records = Orderproduct::query()
//                            ->where(function ($q1)
//                            {
//                                $q1->where('tmp_share_order' , '>' , 0)->orWhereIn('product_id' , [180 , 182]) ;
//                            })
                            ->whereHas('order', function ($q2) use ($timePeriod, $row) {
                                $q2->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                    ->whereIn('paymentstatus_id', [
                                        config('constants.PAYMENT_STATUS_PAID'),
                                        config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                                    ])
//                                    ->whereHas('transactions' , function ($q2)
//                                    {
//                                        $q2->where('cost' , '>' , 0)->where('transactionstatus_id' , config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
//                                            ->where('paymentmethod_id' , '<>' , config('constants.PAYMENT_METHOD_WALLET'));
//                                    })
                                    ->where('completed_at', '>=', $timePeriod['date'].' 00:00:00')
                                    ->where('completed_at', '<=', $timePeriod['date'].' 23:59:59');
                            });
                        $columnData = $records->sum('cost');
                        break;
                    default:
                        $columnData = null;
                        break;
                }

                $rowCollection->push($columnData);
            }

            $rowCollection->prepend($row);
            $table->put($key, $rowCollection);

            $bar->advance();
        }

        $bar->finish();
        return $table;
    }

    private function getShahrivar1402()
    {
        return collect([
            [
                'day' => '1',
                'date' => '2023-08-23',
            ],
            [
                'day' => '2',
                'date' => '2023-08-24',
            ],
            [
                'day' => '3',
                'date' => '2023-08-25',
            ],
            [
                'day' => '4',
                'date' => '2023-08-26',
            ],
            [
                'day' => '5',
                'date' => '2023-08-27',
            ],
            [
                'day' => '6',
                'date' => '2023-08-28',
            ],
            [
                'day' => '7',
                'date' => '2023-08-29',
            ],
            [
                'day' => '8',
                'date' => '2023-08-30',
            ],
            [
                'day' => '9',
                'date' => '2023-08-31',
            ],
            [
                'day' => '10',
                'date' => '2023-09-01',
            ],
            [
                'day' => '11',
                'date' => '2023-09-02',
            ],
            [
                'day' => '12',
                'date' => '2023-09-03',
            ],
            [
                'day' => '13',
                'date' => '2023-09-04',
            ],
            [
                'day' => '14',
                'date' => '2023-09-05',
            ],
            [
                'day' => '15',
                'date' => '2023-09-06',
            ],
            [
                'day' => '16',
                'date' => '2023-09-07',
            ],
            [
                'day' => '17',
                'date' => '2023-09-08',
            ],
            [
                'day' => '18',
                'date' => '2023-09-09',
            ],
            [
                'day' => '19',
                'date' => '2023-09-10',
            ],
            [
                'day' => '20',
                'date' => '2023-09-11',
            ],
            [
                'day' => '21',
                'date' => '2023-09-12',
            ],
            [
                'day' => '22',
                'date' => '2023-09-13',
            ],
            [
                'day' => '23',
                'date' => '2023-09-14',
            ],
            [
                'day' => '24',
                'date' => '2023-09-15',
            ],
            [
                'day' => '25',
                'date' => '2023-09-16',
            ],
            [
                'day' => '26',
                'date' => '2023-09-17',
            ],
            [
                'day' => '27',
                'date' => '2023-09-18',
            ],
            [
                'day' => '28',
                'date' => '2023-09-19',
            ],
            [
                'day' => '29',
                'date' => '2023-09-20',
            ],
            [
                'day' => '30',
                'date' => '2023-09-21',
            ],
            [
                'day' => '31',
                'date' => '2023-09-22',
            ],
        ]);
    }

    private function generateTableOfMonthlyContentsCount(): Collection
    {
        $rows = $this->get99Months();
        $bar = $this->output->createProgressBar($rows->count());
        $table = collect();
        foreach ($rows as $row) {
            $rowCollection = collect();

            $this->info('Processing row'.$row['month']);

            $freeVideos = Content::enable()
                ->whereNull('redirectUrl')
                ->where('contenttype_id', Content::CONTENT_TYPE_VIDEO)
                ->whereBetween('created_at', [$row['periodBegin'], $row['periodEnd']])
                ->where('isFree', 1)
                ->get();

            $paidVideos = Content::whereNull('redirectUrl')
                ->where('contenttype_id', Content::CONTENT_TYPE_VIDEO)
                ->whereBetween('created_at', [$row['periodBegin'], $row['periodEnd']])
                ->where('isFree', 0)
                ->get();

            $rowCollection->push([
                $row['month'], (int) (($freeVideos->sum('duration') / 60) / 60),
                (int) (($paidVideos->sum('duration') / 60) / 60)
            ]);

            $table->push($rowCollection);
            $bar->advance();
        }

        $bar->finish();
        $this->info('Done');
        return $table;
    }

    private function get99Months(): Collection
    {
        return collect([
//            [
//                'month' => 'فروردین 99',
//                'periodBegin' => '2020-03-20',
//                'periodEnd' => '2020-04-20',
//            ],
//            [
//                'month' => 'اردیبهشت 99',
//                'periodBegin' => '2020-04-20',
//                'periodEnd' => '2020-05-21',
//            ],
//            [
//                'month' => 'خرداد 99',
//                'periodBegin' => '2020-05-21',
//                'periodEnd' => '2020-06-21',
//            ],
//            [
//                'month' => 'تیر 99',
//                'periodBegin' => '2020-06-21',
//                'periodEnd' => '2020-07-22',
//            ],
//            [
//                'month' => 'مرداد 99',
//                'periodBegin' => '2020-07-22',
//                'periodEnd' => '2020-08-22',
//            ],
//            [
//                'month' => 'شهریور 99',
//                'periodBegin' => '2020-08-22',
//                'periodEnd' => '2020-09-22',
//            ],
//            [
//                'month' => 'مهر 99',
//                'periodBegin' => '2020-09-22',
//                'periodEnd' => '2020-10-22',
//            ],
//            [
//                'month' => 'آبان 99',
//                'periodBegin' => '2020-10-22',
//                'periodEnd' => '2020-11-21',
//            ],
            [
                'month' => 'آذر 99',
                'periodBegin' => '2020-11-21',
                'periodEnd' => '2020-12-21',
            ],
            [
                'month' => 'دی 99',
                'periodBegin' => '2020-12-21',
                'periodEnd' => '2021-01-20',
            ],
            [
                'month' => 'بهمن 99',
                'periodBegin' => '2021-01-20',
                'periodEnd' => '2021-02-19',
            ],
            [
                'month' => 'اسفند 99',
                'periodBegin' => '2021-02-19',
                'periodEnd' => '2021-03-21',
            ],
            [
                'month' => 'فروردین 1400',
                'periodBegin' => '2021-03-21',
                'periodEnd' => '2021-04-21',
            ],
            [
                'month' => 'اردیبهشت 1400',
                'periodBegin' => '2021-04-21',
                'periodEnd' => '2021-05-22',
            ],
            [
                'month' => 'خرداد 1400',
                'periodBegin' => '2021-05-22',
                'periodEnd' => '2021-06-22',
            ],
            [
                'month' => 'تیر 1400',
                'periodBegin' => '2021-06-22',
                'periodEnd' => '2021-07-23',
            ],
            [
                'month' => 'مرداد 1400',
                'periodBegin' => '2021-07-23',
                'periodEnd' => '2021-08-23',
            ],
            [
                'month' => 'شهریور 1400',
                'periodBegin' => '2021-08-23',
                'periodEnd' => '2021-09-23',
            ],
        ]);
    }

    private function generateTableOfMonthlySaleReport()
    {
        $rows = [
            'number_of_customers' => 'تعداد خریداران', 'number_of_orderproducts' => 'تعداد فروش',
            'number_of_customers_incremental' => 'تجمعی خریداران', 'total_of_transactions' => 'جمع ورودی(تومان)',
            'total_of_instalments' => 'جمع قسط ها(تومان)', 'total_of_base_price' => 'جمع قیمت بدون تخفیف(تومان)'
        ];

        $table = collect();

        $this->info('Making the report ...');
        $bar = $this->output->createProgressBar(count($rows));
        foreach ($rows as $key => $row) {
            $this->info('Processing row '.$key);
            $rowCollection = collect();
            $columns = $this->get99Months();
            foreach ($columns as $key2 => $timePeriod) {
                if ($key2 == 0) {
                    $firstMonth = $timePeriod['periodBegin'];
                }

                $this->info('Processing day '.$timePeriod['month']);
                switch ($key) {
                    case 'number_of_customers' :
                        $records = User::whereHas('orders', function ($q) use ($timePeriod) {
                            $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                ->whereIn('paymentstatus_id', [
                                    config('constants.PAYMENT_STATUS_PAID'),
                                    config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                                ])
                                ->where('completed_at', '>=', $timePeriod['periodBegin'].' 00:00:00')
                                ->where('completed_at', '<=', $timePeriod['periodEnd'].' 00:00:00');
                        })->get();
                        $columnData = $records->count();
                        break;
                    case 'number_of_customers_incremental' :
                        $records = User::whereHas('orders', function ($q) use ($timePeriod, $firstMonth) {
                            $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                ->whereIn('paymentstatus_id', [
                                    config('constants.PAYMENT_STATUS_PAID'),
                                    config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                                ])
//                                ->whereHas('transactions' , function ($q2)
//                                {
//                                    $q2->where('cost' , '>' , 0)
//                                        ->where('transactionstatus_id' , config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
//                                        ->where('paymentmethod_id' , '<>' , config('constants.PAYMENT_METHOD_WALLET'));
//                                })
                                ->where('completed_at', '>=', $firstMonth.' 00:00:00')
                                ->where('completed_at', '<=', $timePeriod['periodEnd'].' 23:59:59');
                        })->get();
                        $columnData = $records->count();
                        break;
                    case 'number_of_orderproducts' :
                        $records = $orderproducts = Orderproduct::query()
                            ->whereHas('order', function ($q2) use ($timePeriod, $row) {
                                $q2->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                    ->whereIn('paymentstatus_id', [
                                        config('constants.PAYMENT_STATUS_PAID'),
                                        config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                                    ])
                                    ->where('completed_at', '>=', $timePeriod['periodBegin'].' 00:00:00')
                                    ->where('completed_at', '<=', $timePeriod['periodEnd'].' 00:00:00');
                            });
                        $columnData = $records->count();
                        break;
                    case 'total_of_transactions' :
                        $records = Transaction::query()
                            ->whereHas('order', function ($q) use ($row) {
                                $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                    ->whereIn('paymentstatus_id', [config('constants.PAYMENT_STATUS_PAID')]);
                            })
                            ->where('completed_at', '>=', $timePeriod['periodBegin'].' 00:00:00')
                            ->where('completed_at', '<=', $timePeriod['periodEnd'].' 00:00:00')
                            ->whereNull('wallet_id')
                            ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                            ->get();
                        $columnData = $records->sum('cost');
                        break;
                    case 'total_of_instalments' :
                        $records = Transaction::query()
                            ->whereHas('order', function ($q) use ($row) {
                                $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                    ->whereIn('paymentstatus_id',
                                        [config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')]);
                            })
                            ->where('completed_at', '>=', $timePeriod['periodBegin'].' 00:00:00')
                            ->where('completed_at', '<=', $timePeriod['periodEnd'].' 00:00:00')
                            ->whereNull('wallet_id')
                            ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                            ->get();
                        $columnData = $records->sum('cost');
                        break;
                    case 'total_of_base_price' :
                        $records = $orderproducts = Orderproduct::query()
                            ->whereHas('order', function ($q2) use ($timePeriod, $row) {
                                $q2->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                                    ->whereIn('paymentstatus_id', [
                                        config('constants.PAYMENT_STATUS_PAID'),
                                        config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                                    ])
                                    ->where('completed_at', '>=', $timePeriod['periodBegin'].' 00:00:00')
                                    ->where('completed_at', '<=', $timePeriod['periodEnd'].' 00:00:00');
                            });
                        $columnData = $records->sum('cost');
                        break;
                    default:
                        $columnData = null;
                        break;
                }

                $rowCollection->push($columnData);
            }

            $rowCollection->prepend($row);

            $table->put($key, $rowCollection);

            $bar->advance();

        }

        $bar->finish();
        return $table;
    }

    private function generateTableOfCoupons1400()
    {
        $startDate = '2021-03-21';  // 1400-01-01

        $resources = Coupon::with(['coupontype'])
            ->where('created_at', '>', $startDate)
            ->get();

        if (!count($resources)) {
            $this->info('Not found any record!');
            return null;
        }

        $table = collect();
        /** @var Coupon $resource */
        foreach ($resources as $resource) {
            $table->push(collect([
                $resource->code,
                $resource->coupontype->displayName,
                $resource->discount,
                $this->convertDate($resource->created_at, 'toJalali').' '.explode(' ', $resource->created_at)[1],
                isset($resource->validUntil) ? $this->convertDate($resource->validUntil, 'toJalali').' '.explode(' ',
                        $resource->validUntil)[1] : null,
                $resource->usageLimit,
            ]));
        }

        return $table;
    }

    private function generateTableOfOrderProducts1400()
    {
        $startDate = '2021-03-21';  // 1400-01-01

        $resources = Orderproduct::withTrashed()
            ->with(['product'])
            ->where('created_at', '>', $startDate)
            ->get();

        if (!count($resources)) {
            $this->info('Not found any record!');
            return null;
        }

        $table = collect();
        /** @var Orderproduct $resource */
        foreach ($resources as $resource) {
            $table->push(collect([
                $resource->id,
                $resource->product->name,
                $resource->discountPercentage,
                $resource->tmp_final_cost,
                is_null($resource->deleted_at) ? 0 : 1,
                $this->convertDate($resource->created_at, 'toJalali').' '.explode(' ', $resource->created_at)[1],
            ]));
        }

        return $table;
    }

    private function generateTableOfPaidOrders1400()
    {
        $startDate = '2021-03-21';  // 1400-01-01
        $paymentPaidStatuses = implode(',', [
            config('constants.PAYMENT_STATUS_INDEBTED'),
            config('constants.PAYMENT_STATUS_PAID'),
            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
        ]);

        $resources = DB::select("
            SELECT
                o.id order_id,
                SUM(IFNULL(total_t.cost, 0)) sum_total_transactions,
                (IFNULL(o.couponDiscount, 0) * IFNULL(o.cost, 0) + IFNULL(o.costwithoutcoupon, 0)) price,
                o.discount order_discount,
                ps.displayName payment_statuses_name,
                c.code coupon_code,
                o.couponDiscount order_coupon_discount,
                SUM(IFNULL(wallet_t.cost, 0)) sum_wallet_transactions,
                SUM(IFNULL(non_wallet_t.cost, 0)) sum_non_wallet_transactions,
                o.created_at order_created_at
            FROM orders o
                LEFT JOIN transactions total_t ON o.id = total_t.order_id
                LEFT JOIN transactions wallet_t ON o.id = wallet_t.order_id AND wallet_t.wallet_id IS NULL AND wallet_t.paymentmethod_id != 5
                LEFT JOIN transactions non_wallet_t ON o.id = non_wallet_t.order_id AND wallet_t.wallet_id IS NOT NULL AND wallet_t.paymentmethod_id = 5
                LEFT JOIN paymentstatuses ps ON o.paymentstatus_id = ps.id
                LEFT JOIN coupons c ON o.coupon_id = c.id
            WHERE
                o.created_at > '{$startDate}' AND
                o.paymentstatus_id IN ($paymentPaidStatuses) AND
                o.deleted_at IS NULL
            GROUP BY o.id, o.couponDiscount, o.cost, o.created_at, o.costwithoutcoupon, o.discount, c.code, c.discount, ps.displayName
        ");

        if (!count($resources)) {
            $this->info('Not found any record!');
            return null;
        }

        $table = collect();
        foreach ($resources as $resource) {
            $firstOrderTransaction = DB::select("
                SELECT t.created_at order_first_transaction_created_at
                FROM orders o
                    LEFT JOIN transactions t ON o.id = t.order_id
                WHERE t.order_id = {$resource->order_id}
                ORDER BY t.created_at
                limit 1
            ");

            $table->push(collect([
                $resource->order_id,
                $resource->sum_total_transactions,
                $resource->price,
                $resource->order_discount,
                $resource->payment_statuses_name,
                $resource->coupon_code,
                $resource->order_coupon_discount,
                $resource->sum_wallet_transactions,
                $resource->sum_non_wallet_transactions,
                $this->convertDate($resource->order_created_at, 'toJalali').' '.explode(' ',
                    $resource->order_created_at)[1],
                isset($firstOrderTransaction[0]) && isset($firstOrderTransaction[0]->order_first_transaction_created_at) ? $this->convertDate($firstOrderTransaction[0]->order_first_transaction_created_at,
                        'toJalali').' '.explode(' ',
                        $firstOrderTransaction[0]->order_first_transaction_created_at)[1] : null,
            ]));
        }

        return $table;
    }

    private function general(array $columns)
    {
        $tempQuery = User::whereHas('orders', function ($query) {
            $query->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
                ->where('completed_at', '>=', '2020-07-23 00:00:00');
        })->get()->unique('mobile');

        if (!$tempQuery->count()) {
            $this->info('No Records!');
            return null;
        }

        $table = collect();
        foreach ($tempQuery as $resource) {
            $tempData = [];
            foreach ($columns as $key => $value) {
                $tempData[] = $resource->$key;
            }
            $table->push(collect($tempData));
        }

        return $table;
    }

    private function registerToPurchasePeriodReport($from, $to): array
    {
        $completePaymentStatus = [
            config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
        ];
        $incompletPaymentStatus = [
            config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_INDEBTED')
        ];

        $result = [];
        $result['abrisham pack complete'] = $this->reportOnProducts(Product::ALL_PACK_ABRISHAM_PRODUCTS, $from, $to,
            paymentStatus: $completePaymentStatus);
        $result['abrisham single complete'] = $this->reportOnProducts(array_keys(Product::ALL_ABRISHAM_PRODUCTS), $from,
            $to, paymentStatus: $completePaymentStatus);
        $result['abrisham pack incomplete'] = $this->reportOnProducts(Product::ALL_PACK_ABRISHAM_PRODUCTS, $from, $to,
            paymentStatus: $incompletPaymentStatus);
        $result['abrisham single incomplete'] = $this->reportOnProducts(Product::ALL_PACK_ABRISHAM_PRODUCTS, $from, $to,
            paymentStatus: $incompletPaymentStatus);
        $result['arash'] = $this->reportOnProducts(Product::ARASH_PRODUCTS_ARRAY, $from, $to);
        $result['taftan'] = $this->reportOnProducts(Product::ALL_TAFTAN_PRODUCTS, $from, $to);
        return $result;
    }

    private function reportOnProducts(
        array $product,
        string $from,
        string $to,
        array $paymentStatus = null,
        array $orderStatus = null
    ) {
        $paymentStatus = $paymentStatus ?? [
            config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
            config('constants.PAYMENT_STATUS_INDEBTED')
        ];
        $orderStatus = $orderStatus ?? [
            config('constants.ORDER_STATUS_CLOSED'), config('constants.ORDER_STATUS_POSTED')
        ];
        $sumDays = 0;
        $orders = OrderRepo::getBasePaymentAndOrderStatus($orderStatus, $paymentStatus, $product)
            ->whereBetween('completed_at', [$from, $to])
            ->get();

        $users = collect();
        foreach ($orders as $order) {
            if ($users->contains($order->user_id)) {
                continue;
            }
            $order = $orders->where('user_id', $order->user_id)->sortBy('created_at')->first();
            $daysFromRegisterToPurchase = Carbon::createFromFormat('Y-m-d H:s:i',
                $order->completed_at)->diffInDays(Carbon::createFromFormat('Y-m-d H:s:i', $order->user->created_at));
            $sumDays += $daysFromRegisterToPurchase;
            $users->push($order->user_id);

        }
        return $orders->count() ? ($sumDays / $users->count()) : 0;
    }
}
