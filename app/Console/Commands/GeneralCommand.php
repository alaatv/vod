<?php

namespace App\Console\Commands;

use App\Classes\AlaaStatistics;
use App\Events\BonyadEhsanUserUpdate;
use App\Exports\DefaultClassExport;
use App\Exports\Rubika\RubikaExport;
use App\Models\_3aExam;
use App\Models\Billing;
use App\Models\BonyadEhsanConsultant;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Coupon;
use App\Models\DanaContentTransfer;

use App\Models\DanaProductContentTransfer;

use App\Models\DanaProductSetTransfer;

use App\Models\DanaSetTransfer;

use App\Models\Employeetimesheet;

use App\Models\Major;
use App\Models\Newsletter;

use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Productvoucher;

use App\Models\ReferralCode;

use App\Models\TempBucketLog;

use App\Models\Timepoint;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductvoucherRepo;
use App\Repositories\UserRepo;
use App\Services\DanaProductService;
use App\Services\DanaService;
use App\Traits\APIRequestCommon;
use App\Traits\DateTrait;
use App\Traits\Helper;
use App\Traits\User\AssetTrait;
use App\Traits\User\ReferralRequestTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Console\Helper\ProgressBar;

class GeneralCommand extends Command
{
    use APIRequestCommon;
    use AssetTrait;
    use DateTrait;
    use Helper;
    use ReferralRequestTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:general';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executing a general code';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    //Giving gifts
    public function handle0()
    {
        $this->info('Giving gifts');
        $productIds = [
            Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
            Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
            Product::RAHE_ABRISHAM1401_PRO_SHIMI,
            Product::RAHE_ABRISHAM1401_PRO_ZIST,
            Product::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN,
            Product::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI,
            Product::RAHE_ABRISHAM1401_ZABAN,
            Product::RAHE_ABRISHAM1401_PRO_ZABAN,
            Product::RAHE_ABRISHAM1401_DINI,
            Product::RAHE_ABRISHAM1401_PRO_DINI,
            Product::RAHE_ABRISHAM1401_ARABI,
            Product::RAHE_ABRISHAM1401_PRO_ARABI,
            Product::RAHE_ABRISHAM1401_ADABIYAT,
            Product::RAHE_ABRISHAM1401_PRO_ADABIYAT,
            Product::RAHE_ABRISHAM1401_PACK_OMOOMI,
            Product::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI,
        ];
        $allGiftIds = [
            Product::EMTEHAN_NAHAYI_1402_SHIMI,
            Product::EMTEHAN_NAHAYI_1402_FIZIK,
            Product::EMTEHAN_NAHAYI_1402_ZIST,
            Product::EMTEHAN_NAHAYI_1402_RIYAZIYAT_TAJROBI,
            Product::EMTEHAN_NAHAYI_1402_HENDESE,
            Product::EMTEHAN_NAHAYI_1402_GOSASTE,
            Product::EMTEHAN_NAHAYI_1402_HESABAN,
            Product::EMTEHAN_NAHAYI_1402_ZABAN,
            Product::EMTEHAN_NAHAYI_1402_DINI,
            Product::EMTEHAN_NAHAYI_1402_ARABI,
            Product::EMTEHAN_NAHAYI_1402_ADABIYAT,
        ];
        $allGifts = Product::whereIn('id', $allGiftIds)->get();
        $orders = Order::where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->whereIn('paymentstatus_id',
                [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID')])
            ->where('seller', 1)
            ->whereHas('orderproducts', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds)
                    ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'));
            })->with('orderproducts')->get();

        $count = $orders->count();
        if (!$this->confirm("$count found, continue?")) {
            return false;
        }

        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();
        foreach ($orders as $order) {
            $orderproducts =
                $order->orderproducts->whereIn('product_id', $productIds)
                    ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'));
            $giftIds = [];
            foreach ($orderproducts as $orderproduct) {
                $giftIds = array_merge($giftIds, $this->determineAbrishamGifts($orderproduct->product_id));
            }

            $gifts = $allGifts->whereIn('id', $giftIds);
            if ($gifts->count() != count($giftIds)) {
                Log::channel('debug')->warning("In GeneralCommand : number of giftIds is not equal to gift collection : order-{$order->id}");
                $this->info("\n");
                $progressBar->advance();
                continue;
            }

            foreach ($gifts as $gift) {
                $giftCost = $gift->price;
                $giftOrderproduct = OrderproductRepo::createGiftOrderproduct($order->id, $gift->id, $giftCost['base']);
                if (is_null($giftOrderproduct)) {
                    Log::channel('debug')->error("In GeneralCommand : Gift was not given : order-{$order->id} : gift-{$gift->id}");
                    $this->info("\n");
                }
            }

            $progressBar->advance();
        }
        $progressBar->finish();
        $this->info('Done!');
        return false;
    }

    //Deleting orderproducts

    private function determineAbrishamGifts(int $productId): array
    {
        switch ($productId) {
            case Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI  : // Riyazi pack
                return [
                    Product::EMTEHAN_NAHAYI_1402_SHIMI, Product::EMTEHAN_NAHAYI_1402_FIZIK,
                    Product::EMTEHAN_NAHAYI_1402_HESABAN, Product::EMTEHAN_NAHAYI_1402_GOSASTE,
                    Product::EMTEHAN_NAHAYI_1402_HENDESE
                ];
            case Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI : //Tajrobi packl
                return [
                    Product::EMTEHAN_NAHAYI_1402_SHIMI, Product::EMTEHAN_NAHAYI_1402_FIZIK,
                    Product::EMTEHAN_NAHAYI_1402_RIYAZIYAT_TAJROBI, Product::EMTEHAN_NAHAYI_1402_ZIST
                ];
            case Product::RAHE_ABRISHAM1401_PRO_SHIMI : //Shimi
                return [Product::EMTEHAN_NAHAYI_1402_SHIMI];
            case Product::RAHE_ABRISHAM1401_PRO_ZIST : //Zist
                return [Product::EMTEHAN_NAHAYI_1402_ZIST];
            case Product::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN : //Fizik
                return [Product::EMTEHAN_NAHAYI_1402_FIZIK];
            case Product::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI  : //Fizik
                return [Product::EMTEHAN_NAHAYI_1402_FIZIK];
            case Product::RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI  : //Riyaziat riyazi
                return [
                    Product::EMTEHAN_NAHAYI_1402_HESABAN, Product::EMTEHAN_NAHAYI_1402_GOSASTE,
                    Product::EMTEHAN_NAHAYI_1402_HENDESE
                ];
            case Product::RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI : //Riyazi tajrobi
                return [Product::EMTEHAN_NAHAYI_1402_RIYAZIYAT_TAJROBI];
            case Product::RAHE_ABRISHAM1401_ZABAN :
            case Product::RAHE_ABRISHAM1401_PRO_ZABAN:
                return [Product::EMTEHAN_NAHAYI_1402_ZABAN];
            case Product::RAHE_ABRISHAM1401_DINI:
            case Product::RAHE_ABRISHAM1401_PRO_DINI:
                return [Product::EMTEHAN_NAHAYI_1402_DINI];
            case Product::RAHE_ABRISHAM1401_ARABI:
            case Product::RAHE_ABRISHAM1401_PRO_ARABI:
                return [Product::EMTEHAN_NAHAYI_1402_ARABI];
            case Product::RAHE_ABRISHAM1401_ADABIYAT:
            case Product::RAHE_ABRISHAM1401_PRO_ADABIYAT:
                return [Product::EMTEHAN_NAHAYI_1402_ADABIYAT];
            case Product::RAHE_ABRISHAM1401_PACK_OMOOMI:
            case Product::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI:
                return [
                    Product::EMTEHAN_NAHAYI_1402_ADABIYAT, Product::EMTEHAN_NAHAYI_1402_ZABAN,
                    Product::EMTEHAN_NAHAYI_1402_DINI, Product::EMTEHAN_NAHAYI_1402_ARABI
                ];
        }

        return [];
    }

    public function handle00()
    {
        $this->info('Deleting orderproducts');
        $productIds = [
            Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
            Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
            Product::RAHE_ABRISHAM1401_PRO_SHIMI,
            Product::RAHE_ABRISHAM1401_PRO_ZIST,
            Product::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN,
            Product::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI,
            Product::RAHE_ABRISHAM1401_ZABAN,
            Product::RAHE_ABRISHAM1401_PRO_ZABAN,
            Product::RAHE_ABRISHAM1401_DINI,
            Product::RAHE_ABRISHAM1401_PRO_DINI,
            Product::RAHE_ABRISHAM1401_ARABI,
            Product::RAHE_ABRISHAM1401_PRO_ARABI,
            Product::RAHE_ABRISHAM1401_ADABIYAT,
            Product::RAHE_ABRISHAM1401_PRO_ADABIYAT,
            Product::RAHE_ABRISHAM1401_PACK_OMOOMI,
            Product::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI,
        ];
        $orders = Order::where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->whereIn('paymentstatus_id',
                [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID')])
            ->where('seller', 1)
            ->whereHas('orderproducts', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds)
                    ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'));
            })->with('orderproducts')->get();

        $count = $orders->count();
        if (!$this->confirm("$count found, continue?")) {
            return false;
        }

        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();
        $counter = 0;
        foreach ($orders as $order) {
            $orderproducts = $order->orderproducts;
            $iteratedOrderproductIds = [];
            foreach ($orderproducts as $orderproduct) {
                if (in_array($orderproduct->id, $iteratedOrderproductIds)) {
                    $progressBar->advance();
                    continue;

                }


                $sameOrderproducts = $order->orderproducts()
                    ->where('id', '<>', $orderproduct->id)
                    ->where('product_id', $orderproduct->product_id)
                    ->where('orderproducttype_id', $orderproduct->orderproducttype_id)
                    ->get();
                foreach ($sameOrderproducts as $sameOrderproduct) {
//                    $sameOrderproduct->delete();
                    $iteratedOrderproductIds[] = $sameOrderproduct->id;
                    $counter++;
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->info("\n");
        $this->info("Total number of orderproducts to delete: {$counter}");

    }

    /**
     * Setting permissions of ticket department
     *
     * @return mixed
     */
    public function handle1()
    {
        $headers = ['سال', 'ستون 1', 'ستون 2', 'ستون 3', 'ستون 4', 'ستون 5', 'ستون 6'];
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_harkatAval_sale_{$now}.xlsx";
        $disk = config('disks.GENERAL');
        $table = $this->generateTableOfIncome();
        Excel::store(new DefaultClassExport($table, $headers), $fileName, $disk);

        $this->info('Done');
        return 0;
    }

    private function generateTableOfIncome(): Collection
    {
        $rows = collect([
            [
                'year' => '1395',
                'caption' => 'سال 1395',
                'begin' => '2016-03-20 00:00:00',
                'end' => '2017-03-21 00:00:00',
            ],
            [
                'year' => '1396',
                'caption' => 'سال 1396',
                'begin' => '2017-03-21 00:00:00',
                'end' => '2018-03-21 00:00:00',
            ],
            [
                'year' => '1397',
                'caption' => 'سال 1397',
                'begin' => '2018-03-21 00:00:00',
                'end' => '2019-03-21 00:00:00',
            ],
            [
                'year' => '1398',
                'caption' => 'سال 1398',
                'begin' => '2019-03-21 00:00:00',
                'end' => '2020-03-20 00:00:00',
            ],
            [
                'year' => '1399',
                'caption' => 'سال 1399',
                'begin' => '2020-03-20 00:00:00',
                'end' => '2021-03-21 00:00:00',
            ],
            [
                'year' => '1400',
                'caption' => 'سال 1400',
                'begin' => '2021-03-21 00:00:00',
                'end' => '2022-03-21 00:00:00',
            ],
        ]);

        $bar = $this->output->createProgressBar($rows->count());
        $table = collect();
        foreach ($rows as $row) {
            $this->info('Processing row '.$row['year']);
            $rowCollection = collect();

            $column1 = 0;

            $yearUsers = User::where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end']);

            $column2 = $yearUsers->count();

            $column3 = $yearUsers->whereHas('orders', function ($q) use ($row) {
                $q->whereHas('transactions', function ($q2) use ($row) {
                    $q2->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                        ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                        ->whereNull('wallet_id')
                        ->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end']);
                });
            })->count();

            $column4 = Transaction::query()
                ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                ->whereNull('wallet_id')
                ->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end'])
                ->whereHas('order', function ($q) use ($row) {
                    $q->whereHas('user', function ($q2) use ($row) {
                        $q2->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end']);
                    });
                })->sum('cost');

            $column5 = User::where('created_at', '<', $row['begin'])
                ->whereHas('orders', function ($q) use ($row) {
                    $q->whereHas('transactions', function ($q2) use ($row) {
                        $q2->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                            ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                            ->whereNull('wallet_id')
                            ->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end']);
                    });
                })->count();

            $column6 = Transaction::query()
                ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                ->whereNull('wallet_id')
                ->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end'])
                ->whereHas('order', function ($q) use ($row) {
                    $q->whereHas('user', function ($q2) use ($row) {
                        $q2->where('created_at', '<', $row['begin']);
                    });
                })->sum('cost');

            $rowCollection->push([$row['year'], $column1, $column2, $column3, $column4, $column5, $column6]);

            $table->push($rowCollection);
            $bar->advance();
            $this->info("\n");
        }

        $bar->finish();
        $this->info("\n");
        return $table;
    }

    public function handle2()
    {
        $headers =
            ['id', 'عنوان', 'ریاضی', 'تجربی', 'انسانی', 'تاریخ درج', 'مجموع فیلم ها', 'تعداد فیلم ها', 'تعداد جزوات'];
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_harkatAval_sets_{$now}.xlsx";
        $disk = config('disks.GENERAL');
        $table = $this->generateTableOfContents();
        Excel::store(new DefaultClassExport($table, $headers), $fileName, $disk);

        $this->info('Done');
        return 0;
    }

    private function generateTableOfContents(): Collection
    {
        $rows = Contentset::whereNull('redirectUrl')->get();

        $bar = $this->output->createProgressBar($rows->count());
        $table = collect();
        foreach ($rows as $key => $row) {
            $rowCollection = collect();

            $column1 = $row->id;

            $column2 = $row->name;

            $tags = $row->tags?->tags;
            if (!isset($tags)) {
                $column3 = 'نامشخص';
                $column4 = 'نامشخص';
                $column5 = 'نامشخص';
            } else {
                $column3 = array_search('رشته_ریاضی', $tags) ? 1 : 0;
                $column4 = array_search('رشته_تجربی', $tags) ? 1 : 0;
                $column5 = array_search('رشته_انسانی', $tags) ? 1 : 0;
            }

            $column6 = $this->obtainJalaliYear($row->created_at);

            $durationSeconds = $row->contents->where('contenttype_id', 8)->sum('duration');
            $column7 = gmdate('H:i:s', $durationSeconds);

            $column8 = $row->contents->where('contenttype_id', 8)->count();

            $column9 = $row->contents->where('contenttype_id', 1)->count();

            $rowCollection->push([
                $column1, $column2, $column3, $column4, $column5, $column6, $column7, $column8, $column9
            ]);

            $table->push($rowCollection);
            $bar->advance();

        }

        $bar->finish();
        return $table;
    }

    private function obtainJalaliYear($date)
    {
        $explodedDate = explode(' ', $date);
        $explodedDate = $explodedDate[0];
        $explodedDate = explode('-', $explodedDate);
        $year = $explodedDate[0];
        $month = $explodedDate[1];
        $day = $explodedDate[2];

        $date = $this->gregorian_to_jalali($year, $month, $day);
        return $date[0];
    }

    public function handle3()
    {
        $headers = ['دسته محصول', 'درآمد'];
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_harkatAval_categories_{$now}.xlsx";
        $disk = config('disks.GENERAL');
        $table = $this->generateTableOfProductCategoryIncome();
        Excel::store(new DefaultClassExport($table, $headers), $fileName, $disk);

        $this->info('Done');
        return 0;
    }

    private function generateTableOfProductCategoryIncome(): Collection
    {
        /** @var Orderproduct $orderproduct */

        $years = collect([
            [
                'year' => '1395',
                'caption' => 'سال 1395',
                'begin' => '2016-03-20 00:00:00',
                'end' => '2017-03-21 00:00:00',
            ],
            [
                'year' => '1396',
                'caption' => 'سال 1396',
                'begin' => '2017-03-21 00:00:00',
                'end' => '2018-03-21 00:00:00',
            ],
            [
                'year' => '1397',
                'caption' => 'سال 1397',
                'begin' => '2018-03-21 00:00:00',
                'end' => '2019-03-21 00:00:00',
            ],
            [
                'year' => '1398',
                'caption' => 'سال 1398',
                'begin' => '2019-03-21 00:00:00',
                'end' => '2020-03-20 00:00:00',
            ],
            [
                'year' => '1399',
                'caption' => 'سال 1399',
                'begin' => '2020-03-20 00:00:00',
                'end' => '2021-03-21 00:00:00',
            ],
            [
                'year' => '1400',
                'caption' => 'سال 1400',
                'begin' => '2021-03-21 00:00:00',
                'end' => '2022-03-21 00:00:00',
            ],
        ]);

        $rows = [
            'Donation',
            'VIP',
            'آزمون/سه آ',
            'تخفیف یلدا 1400',
            'تلسکوپ',
            'جزوه',
            'سه‌آ/4K',
            'قدیم',
            'متوسطه دوم/گام به گام',
            'همایش/آرش',
            'همایش/امتحان نهایی',
            'همایش/تایتان',
            'همایش/تتا',
            'همایش/تفتان',
            'همایش/گدار',
        ];


        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();
        $this->info("\n");
        $table = collect();
        foreach ($rows as $key => $row) {
            $this->info('Processing category '.$row);
            $rowCollection = collect();

            $rowData = [$row];

            foreach ($years as $year) {
                $this->info('Processing year '.$year['year']);
                $sum = 0;
                $i = 1;
                Orderproduct::query()
                    ->whereHas('product', function ($q) use ($row) {
                        $q->where('category', $row);
                    })->whereHas('order', function ($q2) use ($year) {
                        $q2->where('completed_at', '>=', $year['begin'])
                            ->where('completed_at', '<', $year['end'])
                            ->whereHas('transactions', function ($q3) {
                                $q3->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                                    ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'));
                            });
                    })->chunk(500, function ($orderproducts) use (&$sum, &$i) {
                        $this->info('chunk '.$i);
                        foreach ($orderproducts as $orderproduct) {
                            if (!isset($orderproduct->order)) {
                                continue;
                            }
                            $sum += $orderproduct->getSharedCostOfTransaction();
                        }
                        $i++;
                    });

                $count = User::query()->whereHas('orders', function ($q) use ($year, $row) {
                    $q->where('completed_at', '>=', $year['begin'])
                        ->where('completed_at', '<', $year['end'])
                        ->whereHas('transactions', function ($q3) use ($row) {
                            $q3->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                                ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'));
                        })->whereHas('orderproducts', function ($q2) use ($row) {
                            $q2->whereHas('product', function ($q) use ($row) {
                                $q->where('category', $row);
                            });
                        });
                })->count();

                $rowData[] = $sum;
                $rowData[] = $count;
            }

            $rowCollection->push($rowData);

            $table->push($rowCollection);
            $bar->advance();
            $this->info("\n");

        }

        $bar->finish();
        $this->info("\n");
        return $table;
    }

    public function handle4()
    {
        $headers =
            [
                'دسته محصول', 'جمع 1395', 'تعداد 1395', 'جمع 1396', 'تعداد 1396', 'جمع 1397', 'تعداد 1397', 'جمع 1398',
                'تعداد 1398', 'جمع 1399', 'تعداد 1399', 'جمع 1400', 'تعداد 1400'
            ];
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_harkatAval_categories_{$now}.xlsx";
        $disk = 'DefaultClassExport';
        $table = $this->generateTableOfProductCategoryIncome();
        Excel::store(new DefaultClassExport($table, $headers), $fileName, $disk);

        $this->info('Done');
        return 0;
    }

    public function handle5()
    {
        $orders = Order::whereIn('orderstatus_id', Order::getDoneOrderStatus())
            ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
            ->whereNull('completed_at')->get();
        $count = $orders->count();

        if (!$this->confirm("$count orders found , do you wish to continue?")) {
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $this->info("\n");
        /** @var Order $order */
        foreach ($orders as $order) {
            $transactions =
                $order->transactions()->where('transactionstatus_id',
                    config('constants.TRANSACTION_STATUS_SUCCESSFUL'))->orderBy('created_at')->get();
            if ($transactions->isEmpty()) {
                $this->info('order '.$order->id.' has no transactions');
                continue;
            }

            $firstTransaction = $transactions->first();
            $order->completed_at = $firstTransaction->created_at;
            $order->updateWithoutTimestamp();

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n");
        $this->info('Done');
        return 0;
    }

    public function handle6()
    {
        $totalRegistered = 0;
        $dahom = 0;
        $yazdahom = 0;
        $davazdahom = 0;
        $totalAbrishamCustomers = 0;

        $newsletters = Newsletter::all();
        $registeredUsers = User::whereIn('mobile', $newsletters->pluck('mobile')->toArray())->get();
        $totalNewsletters = $newsletters->count();

        if (!$this->confirm("$totalNewsletters records found , Do you wish to continue?")) {
            $this->info('Aborted');
            return 0;
        }

        $bar = $this->output->createProgressBar($totalNewsletters);
        $this->info("\n");
        foreach ($newsletters as $newsletter) {
            $registeredUser = $registeredUsers->where('mobile', $newsletter->mobile)->first();

            if (!isset($registeredUser)) {
                $bar->advance();
                continue;
            }

            $totalRegistered++;

            $hasAbrisham = Order::query()->where('user_id', $registeredUser->id)->paidAndClosed()
                ->whereHas('orderproducts', function ($q) {
                    $q->whereIn('product_id', array_keys(Product::ALL_ABRISHAM_PRODUCTS))
                        ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                        ->where('cost', '>', 0);
                })->exists();

            if ($hasAbrisham) {
                switch ($newsletter->grade_id) {
                    case 1:
                        $dahom++;
                        break;
                    case 2:
                        $yazdahom++;
                        break;
                    case 8:
                        $davazdahom++;
                }
            }


            $bar->advance();
        }
        $bar->finish();

        $this->info("\n");
        $this->info('total newsletters: '.$totalNewsletters);
        $this->info('total registered: '.$totalRegistered);
        $this->info('total dahom: '.$dahom);
        $this->info('total yazdahom: '.$yazdahom);
        $this->info('total davazdahom: '.$davazdahom);
        $this->info('total Abrisham customers: '.$totalAbrishamCustomers);
        $this->info('Done!');
    }

    public function handle7()
    {
        $this->info('Running ticket department access management');
//         $itemTagsArray = [11 , 752898 , 752898 , 775694 , 947310 , 1141445 , 768928 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741]; // EDUCATION_DEPARTMENT = 1
//           $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// FINANCIAL_DEPARTMENT = 2
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 , 15381 ,  2075839 , 2080802 , 4514];// EMPLOYMENT_DEPARTMENT = 3
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// PARCHAM_DEPARTMENT = 4
//         $itemTagsArray = [11 , 11,499326,752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// RAHE_ABRISHAM_DEPARTMENT = 5
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// FANI_DEPARTMENT = 6
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// MOSHAVERE_KHARID_DEPARTMENT = 7
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// MOSHKELAT_MOHRAVA_RAYGAN = 8
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// MOSHKELAT_MOHTAVA_POOLI = 9
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// TAMAS_BA_MA = 10
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// HEMAYAT_MARDOMI = 11
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514];// ACCOUNT_TRANSFER = 12
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// TAFTAN = 13
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// ARASH = 14
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// TETA = 15
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// 3A = 16
//         $itemTagsArray = [11 , 752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514 , 2128728 , 2125741];// HEKMAT = 17
//         $itemTagsArray = [1];// INTERNAL_FANI_DEPARTMENT = 18
//         $itemTagsArray = [752898,768928,775694,947310,1141445 ,  2075839 , 2080802 , 4514];// INTERNAL_FINANCIAL = 19
//         $itemTagsArray = [1141445, 947310,  775694, 768928, 752898, 2075839, 2080802, 1814381, 2117077, 2117078, 1273453, 4514 , 2128728 , 2125741];// ABRISHAM_PRO = 23
//         $itemTagsArray = [ 752898 , 768928 ,775694 , 1141445 , 947310 ,  2075839 , 2080802 , 2125741 , 2128728 ,1 ,2];// TRAIKH_ENGHEZA = 24
        $itemTagsArray = [1, 2, 10, 1814381, 4514];// ENTEKHAB RESHTE = 25


        $depIds = [
//            TicketDepartment::EDUCATION_DEPARTMENT,
//            TicketDepartment::FINANCIAL_DEPARTMENT,
//            TicketDepartment::EMPLOYMENT_DEPARTMENT,
//            TicketDepartment::PARCHAM_DEPARTMENT,
//            TicketDepartment::RAHE_ABRISHAM_DEPARTMENT,
//            TicketDepartment::FANI_DEPARTMENT,
//            TicketDepartment::MOSHAVERE_KHARID_DEPARTMENT,
//            TicketDepartment::MOSHKELAT_MOHRAVA_RAYGAN,
//            TicketDepartment::MOSHKELAT_MOHTAVA_POOLI,
//            TicketDepartment::TAMAS_BA_MA,
//            TicketDepartment::HEMAYAT_MARDOMI,
//            TicketDepartment::ACCOUNT_TRANSFER,
//            TicketDepartment::TAFTAN_DEPARTMENT,
//            TicketDepartment::ARASH_DEPARTMENT,
//            TicketDepartment::TETA_DEPARTMENT,
//            TicketDepartment::_3A_DEPARTMENT,
//            TicketDepartment::HEKMAT_DEPARTMENT,
//            TicketDepartment::INTERNAL_FANI_DEPARTMENT,
//            TicketDepartment::INTERNAL_FINANCIAL_DEPARTMENT,
//            23,
//              24,
            25,
        ];

        if (!isset($itemTagsArray)) {
            $this->info('There is no selected ticket departments');
            return false;
        }

        foreach ($depIds as $depId) {
            $params = [
                'tags' => json_encode($itemTagsArray, JSON_UNESCAPED_UNICODE),
            ];

            $response =
                $this->sendRequest(config('constants.TAG_API_URL').'id/ticketDepartment/'.$depId, 'PUT', $params);
            if ($response['statusCode'] != Response::HTTP_OK) {
                $this->info('Error on tagging ticket');
                continue;
            }

            $this->info('Permission granted for these users: '.implode(',', $itemTagsArray));
            $this->info('For department :'.$depId);
        }

        $this->info('Done!');
    }

    public function handle8()
    {
        $this->handleRubika();
    }

    public function handle9()
    {

//        $sets = Contentset::findMany([
//            777,
//            784,
//            789,
//            799,
//            807,
//            811,
//            814,
//            820,
//            823,
//            824,
//            827,
//            828,
//            1086,
//            1087,
//            1102,
//            1103,
//            1106,
//            1107,
//            1108,
//            1109,
//            1111,
//            1115,
//            1120,
//            1121,
//            1488,
//            1489,
//            1490,
//            1491,
//            1492,
//            1493,
//            1494,
//            1495,
//            1496,
//            1497,
//            1498,
//            1499,
//            1500,
//            1501,
//            1502,
//            1503,
//            1509,
//            1510,
//            1511,
//            1512,
//            1513,
//            1514,
//            1515,
//            1516,
//            1517,
//            1518,
//            1519,
//            1520,
//            1521,
//            1522,
//            1523,
//            1524,
//            1525,
//            1526,
//            1527,
//            1528,
//            1530,
//            1531,
//            1532,
//            1533,
//            1535,
//            1536,
//            1542,
//        ]); //Emtehan Nahayi
        $sets = Contentset::findMany([
            97, 142, 1241,
        ]); //Emtehan Nahayi

        //$sets = Contentset::findMany([ 1169 , 1170 , 1171 , 1172 , 1181 , 1182 , 1183 , 1184 , 1185 , 1186 ]); // پس_آزمون_گزینه_دو
        //$sets = Contentset::findMany([ 947 , 949 , 969 , 1013 , 1042 , 1047 , 1085 , ]); // متوسطه اول (1)
        //$sets = Contentset::findMany([ 671 , 678 , 686 , 694 , 698 , 727 , 747 , 1011  , 1012 , 1014]); // محصول گدار (1)
        //$sets = Contentset::findMany([ 137 , 169 , 207 , 214 , 217 , 218 , 219 , 584 , 592 , 603 , 605 , 609 , 770 , 817 , 826 , 868 , 948 , 952 , 953 , 957 , 958 , 960 , 961 , 962 ,  968 , 970 , 1001 , 1003 , 1004 , 1005 , 1041 , 1082 , 1100 , 1101 , 1110 , 1353 ]); //  متوسطه دوم (1)
        //$sets = Contentset::findMany([ 1177 , 1178 , 1179 , 1180 , 1187 , 1188 , 1189 , 1190 , 1191 , 1192 ,  ]); // پس_آزمون_قلمچی

        if ($sets->isEmpty()) {
            $this->info('No sets found');
            return 0;
        }

        $fileName = 'rubika-'.now()->timestamp.'.xlsx';

        Excel::store(new RubikaExport($sets), $fileName, config('disks.GROUP_REGISTRATION_REPORT_MINIO'));
    }

    public function handle10()
    {
        $proAbrishamPackOrders = Order::query()->paidAndClosed()->whereHas('orderproducts', function ($q) {
            $q->whereIn('product_id', [
                Product::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI,
                Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
                Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
            ]);
        })->get();

        $count = $proAbrishamPackOrders->count();
        if (!$this->confirm("$count orders found , Do you wish to continue?")) {
            return false;
        }

        $bar = $this->output->createProgressBar();
        /** @var Order $order */
        foreach ($proAbrishamPackOrders as $order) {
            $this->info($order->user->mobile);
//            event(new SendOrderNotificationsEvent($order, $order->user, true));
//            $bar->advance();
        }

        $bar->finish();
        $this->info('DONE');
    }

    public function handle11()
    {
        $this->info('Generating marketing report');
        $productIds = [1101, 1100, 1099, 1098, 1095, 1094, 1093, 1092, 1091, 1090,];

        $users = User::with([
            'orders' => function ($query) use ($productIds) {
                return $query->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                    ->whereIn('paymentstatus_id', [config('constants.PAYMENT_STATUS_PAID')])
//                    ->where('completed_at', '>=', '2022-05-30 00:00:00')
                    ->whereHas('normalOrderproducts', function ($query) use ($productIds) {
                        $query->whereIn('product_id', $productIds);
                    });
            },
        ])
            ->whereHas('orders', function ($query) use ($productIds) {
                $query->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                    ->whereIn('paymentstatus_id', [config('constants.PAYMENT_STATUS_PAID')])
//                    ->where('completed_at', '>=', '2023-06-12 16:00:00')
                    ->whereHas('transactions', function ($query) {
                        $query->where('cost', '>', 0)
                            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                            ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'));
                    })
                    ->whereHas('normalOrderproducts', function ($query) use ($productIds) {
                        $query->whereIn('product_id', $productIds);
                    });
            })->whereDoesntHave('roles')
            ->get();

        $hekmatCoupons = Coupon::select('id')->whereIn('code', ['hekmat50', 'hekmat40'])
            ->pluck('id')->toArray();

        $bar = $this->output->createProgressBar($users->count());
        $collect = [];

        foreach ($users as $key => $user) {
            $collect[$key]['fullName'] = $user->fullName;
            $collect[$key]['order_completed_at'] = implode(' , ', $user->orders->map(function ($order) {
                return $order->convertDate($order->completed_at, 'toJalali');
            })->toArray());
            $collect[$key]['mobile'] = $user->mobile;
            $collect[$key]['products'] =
                implode(' , ', $user->orders->map(function ($order) use ($productIds) {
                    return $order->normalOrderproducts
//                        ->whereIn('product_id', $productIds)
                        ->map(function ($orderproduct) {
                            return $orderproduct->product->name;
                        });
                })->flatten()->toArray());

            $collect[$key]['buy_with_hekmat_coupon'] =
                implode(' , ', $user->orders->map(function ($order) use ($hekmatCoupons) {
                    return in_array($order->coupon_id, $hekmatCoupons) ? 'بله' : 'خیر';
                })->toArray());

            $bar->advance();
        }
        $bar->finish();

        //Export as xlsx
        $disk = config('disks.GENERAL');
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_users_with_abrisham_products_$now.xlsx";
        $headers =
            [
                'نام و نام خانوادگی', 'تاریخ ثبت سفارش', 'شماره موبایل', 'عنوان محصولات خریداری شده',
                'استفاده از حکمت کارت ؟'
            ];
        Excel::store(new DefaultClassExport(collect($collect), $headers), $fileName, $disk);

        $this->info('Done');
        return 0;
    }

    public function handle12()
    {
        $users = User::with([
            'roles' => function ($query) {
                return $query->whereIn('id', [113, 124, 123, 130, 131]);
            },
        ])->whereHas('roles', function ($query) {
            return $query->whereIn('id', [113, 124, 123, 130, 131]);
        })->get();

        $count = $users->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }

        $bar = $this->output->createProgressBar($count);
        $collect = [];
        foreach ($users as $key => $user) {
            if ($user->roles()->whereIn('id', [113, 124, 123, 130, 131])->get()->count() >= 2) {
                $collect[$key]['full_name'] = $user->fullName;
                $collect[$key]['national_code'] = $user->nationalCode;
                $collect[$key]['role'] =
                    implode(' , ',
                        $user->roles()->whereIn('id', [113, 124, 123, 130, 131])->get()->map(function ($role) {
                            return $role->display_name;
                        })->toArray());
            }
            $bar->advance();
        }
        $bar->finish();

        $disk = config('disks.GENERAL');
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_bonyad_users_roles_$now.xlsx";
        $headers = ['نام و نام خانوادگی', 'کد ملی', 'نقش ها'];
        Excel::store(new DefaultClassExport(collect($collect), $headers), $fileName, $disk);

        $this->info('Done');
        return 0;
    }

    public function handle13()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('id', [113, 130, 131, 124]);
        })->get();

        $count = $users->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }

        $bar = $this->output->createProgressBar($count);
        foreach ($users as $user) {
            $consultant = BonyadEhsanConsultant::find($user->id);
            if (!empty($consultant)) {
                continue;
            }

            $this->warn("user with id={$user->id} does not have consultant row. we will create it");
            $consultantUser = $user->consultant()->create(['student_register_limit' => 0]);
            if (!$consultantUser) {
                $this->error("error within creating consultant row for user {$user->id}");
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('Done');
        return 0;
    }

    public function handle14()
    {
        $orders = Order::with('orderproducts')->get();
        $count = $orders->count();

        if (!$this->confirm("$count found , continue?")) {
            return false;
        }


        $bar = $this->output->createProgressBar($count);
        foreach ($orders as $order) {
            $orderProducts = $order->orderproducts->toArray();
            foreach ($orderProducts as $key => $orderProduct) {
                $productId = $orderProduct['product']['id'];
                $orderProductTypeName = $orderProduct['orderproducttype']['name'];
                unset($orderProducts[$key]);
                foreach ($orderProducts as $product) {
                    if ($product['product']['id'] == $productId and $product['orderproducttype']['name'] == $orderProductTypeName) {
                        Orderproduct::destroy($product['id']);
                    }
                }
            }
            $bar->advance();
        }
        $bar->finish();
    }

    //transfer files from one bucket to another

    public function handle15()
    {
        $hedayati = (int) $this->ask('hedayati id = ?');
        $bonyadRoles = [113, 123, 124, 130, 131];
        $userIds = UserRepo::subsetsUserIds([$hedayati], collect([$hedayati]));
        $users = User::whereHas('roles', function ($query) use ($bonyadRoles) {
            return $query->whereIn('id', $bonyadRoles);
        })->whereNotIn('id', $userIds)->get();

        $count = $users->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }

        $bar = $this->output->createProgressBar($count);
        $users->map(function ($user) use ($bonyadRoles, $bar) {
            $user->roles()->detach($bonyadRoles);
            $bar->advance();
        });
        $bar->finish();
        $this->info('done');
        return 0;
    }

    //refactor db table

    public function handle16()
    {
        $orders =
            Order::whereOrderstatusId(config('constants.ORDER_STATUS_CLOSED'))->doesntHave('orderproducts')->doesntHave('transactions');
        $count = $orders->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $orders->delete();
        $this->info('done');
        return 0;
    }

    //check database refactor

    public function handle17()
    {
        $users = User::whereHas('roles', function ($query) {
            return $query->whereIn('id', [113, 123, 124, 130, 131]);
        })->get();
        $count = $users->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($users as $user) {
            if (is_null($user->inserted_by)) {
                continue;
            }
            $user->parents()->sync(UserRepo::parentUsersForSync($user->inserted_by));
            $bar->advance();
        }
        $bar->finish();
        $this->info('done');
        return 0;
    }

    //add product to major=3 for bonyad

    public function handle18()
    {
        $bonyadOrders = Order::wherePaymentstatusId(config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'))
            ->where('coupon_id', 174452)
            ->where('completed_at', '>=', '2022-06-22 00:00:00')
            ->get();

        $count = $bonyadOrders->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }

        $bar = $this->output->createProgressBar($count);
        $processedCount = 0;
        foreach ($bonyadOrders as $bonyadOrder) {
            $user = $bonyadOrder->user;
            $majorId = $user->major_id;
            if (!in_array($majorId, [Major::ENSANI, Major::TAJROBI, Major::RIYAZI])) {
                $bar->advance();
                continue;
            }
            if ($majorId == Major::RIYAZI) {
                $productId = 804;
            } else {
                if ($majorId == Major::TAJROBI) {
                    $productId = 819;
                } else {
                    if ($majorId == Major::ENSANI) {
                        $productId = 834;
                    }
                }
            }
            $hasExam = $user->userHasAnyOfTheseProducts([$productId]);
            if ($hasExam) {
                $bar->advance();
                continue;
            }

            $orderproduct = OrderproductRepo::createGiftOrderproduct($bonyadOrder->id, $productId, 0);
            $processedCount++;
            $bar->advance();
        }
        $bar->finish();
        $this->info("\n");
        $this->info('processed : '.$processedCount);
        $this->info('done');
        return 0;
    }

    // add service id to permissions

    public function handle19()
    {
        //first report
        $users = User::whereHas('orders', function ($query) {
            return $query->whereIn('paymentstatus_id', [2, 4])->whereHas('orderproducts', function ($query) {
                return $query->where('orderproducttype_id',
                    config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))->whereIn('product_id', [758, 757, 756]);
            });
        })->get();
        $this->info('first report');
        $this->info('number of users that bought abrisham pro products with instalment : '.count($users));
        $this->info("\n");

        // second report
        $totalCost = 0;
        $usersBoughtAbrishamProConvert = User::with([
            'orders' => function ($query) {
                return $query->whereHas('billings', function ($query) {
                    return $query->whereIn('p_id', [771, 770, 769, 768, 767, 766, 765, 764, 763, 762, 761, 760, 759]);
                });
            },
            'orders.billings' => function ($query) {
                return $query->whereIn('p_id', [771, 770, 769, 768, 767, 766, 765, 764, 763, 762, 761, 760, 759]);
            },
        ])
            ->whereHas('orders', function ($query) {
                return $query->whereHas('billings', function ($query) {
                    return $query->whereIn('p_id', [771, 770, 769, 768, 767, 766, 765, 764, 763, 762, 761, 760, 759]);
                });
            })->get();
        foreach ($usersBoughtAbrishamProConvert as $user) {
            foreach ($user->orders as $order) {
                foreach ($order->billings as $billing) {
                    $totalCost += $billing->op_share_amount;
                }
            }
        }
        $this->info('second report');
        $this->info('number of users that bought abrisham conversion to abrisham pro product : '.count($usersBoughtAbrishamProConvert).' ---- total deposit amount : '.$totalCost);
        $this->info("\n");

        // third report
        $registrationUsers =
            User::whereDate('created_at', '>=', '2020-06-21 00:00:00')->whereDate('created_at', '<=', now())->get();
        $this->info('third report');
        $this->info('the numbers of users who registered on the site and application from 1401-04-01 till today with twelfth grade : '.count($registrationUsers->where('grade_id',
                8)->where('created_at', '>=', '2022-06-22 00:00:00')));
        $this->info('the numbers of users who registered on the site and application from 1400-04-01 till 1401-03-01 with twelfth grade : '.count($users->where('grade_id',
                8)->where('created_at', '>=', '2021-06-22 00:00:00')->where('created_at', '<', '2022-05-22')));
        $this->info('the numbers of users who registered on the site and application from 1400-04-01 till 1401-03-01 with eleventh grade : '.count($users->where('grade_id',
                2)->where('created_at', '>=', '2021-06-22 00:00:00')->where('created_at', '<', '2022-05-22')));
        $this->info('the numbers of users who registered on the site and application from 1399-04-01 till 1400-03-01 with tenth grade : '.count($users->where('grade_id',
                1)->where('created_at', '>=', '2020-06-21 00:00:00')->where('created_at', '<', '2021-05-22')));
        $this->info('the numbers of users who registered on the site and application from 1041-04-01 till today with graduate of new system grade : '.count($users->where('grade_id',
                9)->where('created_at', '>=', '2022-06-22 00:00:00')));
    }

    public function handle20()
    {
        $fromBucket = 'paid';
        $toBucket = 'alaaTv';
        $files = Storage::disk($fromBucket)->allFiles();
        foreach ($files as $file) {
            try {
                $fileName = $file;
                $content =
                    Content::whereJsonContains('file', [['fileName' => $fileName]])->get(['contentset_id', 'file']);
                if ($content->count() > 1) {
                    TempBucketLog::create([
                        'cat' => 1, 'file_url' => $file, 'error_detail' => 'has mor than one contentset_id'
                    ]);
                    continue;
                }
                if ($content->isEmpty()) {
                    TempBucketLog::create(['cat' => 1, 'file_url' => $file, 'error_detail' => 'contentset_id not set']);
                    continue;
                }
                $content = $content->first();
                foreach (json_decode($content->getAttributes()['file']) as $row) {
                    if ($row->fileName == $fileName) {
                        $path = $content->contentset_id;
                        if ($row->ext == 'mp4' or $row->ext == 'mkv') {
                            switch ($row->res) {
                                case '720p':
                                    $path .= '/HD_720p';
                                    break;
                                case '480p':
                                    $path .= '/hq';
                                    break;
                                case '240p':
                                    $path .= '/240p';
                                    break;
                                default:
                                    TempBucketLog::create([
                                        'cat' => 2, 'file_url' => $file,
                                        'error_detail' => $row->res.' quality not defined'
                                    ]);
                                    continue 2;
                            }
                        }
                        $originalFileName = substr($fileName, strrpos($fileName, '/'));
                        $path .= $originalFileName;
                        if (Storage::disk($toBucket)->has($path) == false) {
                            Storage::disk($toBucket)->writeStream($path, Storage::disk($fromBucket)->readStream($file));
                            if (Storage::disk($toBucket)->has($path) == false) {
                                TempBucketLog::create([
                                    'cat' => 5, 'file_url' => $file,
                                    'error_detail' => 'file does not transfer to new bucket'
                                ]);
                            } else {
                                $toBucketName = Storage::disk($toBucket)->getAdapter()->getBucket();
                                Storage::disk('general')->append('fileTransfer.txt',
                                    "\"{$file}\" => \"{$toBucketName}/{$path}\"", PHP_EOL);
                            }
                        } else {
                            TempBucketLog::create([
                                'cat' => 3, 'file_url' => $file, 'error_detail' => 'file exist in bucket'
                            ]);
                        }
                        break;
                    }
                }
            } catch (Exception $exception) {
                TempBucketLog::create([
                    'cat' => 4, 'file_url' => $file,
                    'error_detail' => 'code='.$exception->getCode().'-message='.$exception->getMessage()
                ]);
            }
        }
        $this->info('done of first move');
        $totalTry = 20;
        while ($totalTry > 0) {
            $errorFiles = TempBucketLog::where('status_of_retry', false)->where(function ($query) {
                $query->where('cat', 4)
                    ->orWhere('cat', 5);
            })->get();
            if ($errorFiles->isEmpty()) {
                break;
            }
            foreach ($errorFiles as $errorFile) {
                $file = $errorFile->file_url;
                try {
                    $fileName = $file;
                    $content =
                        Content::whereJsonContains('file', [['fileName' => $fileName]])->get(['contentset_id', 'file']);
                    if ($content->count() > 1) {
                        $errorFile->update([
                            'cat' => 1,
                            'error_detail' => 'has mor than one contentset_id',
                        ]);
                        continue;
                    }
                    if ($content->isEmpty()) {
                        $errorFile->update([
                            'cat' => 1,
                            'error_detail' => 'contentset_id not set',
                        ]);
                        continue;
                    }
                    $content = $content->first();
                    foreach (json_decode($content->getAttributes()['file']) as $row) {
                        if ($row->fileName == $fileName) {
                            $path = $content->contentset_id;
                            if ($row->ext == 'mp4' or $row->ext == 'mkv') {
                                switch ($row->res) {
                                    case '720p':
                                        $path .= '/HD_720p';
                                        break;
                                    case '480p':
                                        $path .= '/hq';
                                        break;
                                    case '240p':
                                        $path .= '/240p';
                                        break;
                                    default:
                                        $errorFile->update([
                                            'cat' => 2,
                                            'error_detail' => $row->res.' quality not defined',
                                        ]);
                                        continue 2;
                                }
                            }
                            $originalFileName = substr($fileName, strrpos($fileName, '/'));
                            $path .= $originalFileName;
                            if (Storage::disk($toBucket)->has($path) == false) {
                                Storage::disk($toBucket)->writeStream($path,
                                    Storage::disk($fromBucket)->readStream($file));
                                if (Storage::disk($toBucket)->has($path) == false) {
                                    $errorFile->update([
                                        'cat' => 5,
                                        'error_detail' => 'file does not transfer to new bucket',
                                    ]);
                                } else {
                                    $toBucketName = Storage::disk($toBucket)->getAdapter()->getBucket();
                                    Storage::disk('general')->append('fileTransfer.txt',
                                        "\"{$file}\" => \"{$toBucketName}/{$path}\"", PHP_EOL);
                                    $errorFile->update([
                                        'status_of_retry' => 1,
                                    ]);
                                }

                            } else {
                                $errorFile->update([
                                    'cat' => 3,
                                    'error_detail' => 'file exist in bucket',
                                ]);
                            }
                            break;
                        }
                    }
                } catch (Exception $exception) {
                    $errorFile->update([
                        'cat' => 4,
                        'error_detail' => 'code='.$exception->getCode().'-message='.$exception->getMessage(),
                    ]);
                }
            }
            $totalTry--;
            $this->info(10 - $totalTry);

        }
        $this->info('total done');
    }

    public function handle21()
    {
        $fromBucket = 'testBucket';
        $files = Storage::disk($fromBucket)->allFiles('420');
        foreach ($files as $file) {
            try {
                $fileName = '/paid/'.$file;
                $content =
                    Content::whereJsonContains('file', [['fileName' => $fileName]])->get([
                        'id', 'contentset_id', 'file'
                    ]);
                if ($content->count() > 1) {
                    if (TempBucketLog::where('file_url', $file)->get()->isEmpty()) {
                        TempBucketLog::create([
                            'cat' => 6, 'file_url' => $file,
                            'error_detail' => 'has mor than one contentset_id and does not have log before!'
                        ]);
                    }
                    continue;
                }
                if ($content->isEmpty()) {
                    if (TempBucketLog::where('file_url', $file)->get()->isEmpty()) {
                        TempBucketLog::create([
                            'cat' => 6, 'file_url' => $file,
                            'error_detail' => 'contentset_id not set and does not have log before!'
                        ]);
                    }
                    continue;
                }
                $content = $content->first();
                $jsonColumn = collect([]);
                foreach (json_decode($content->getAttributes()['file']) as $row) {
                    if ($row->fileName == $fileName) {
                        $originalFileName = substr($fileName, strrpos($fileName, '/') + 1);
                        $row->fileName = $originalFileName;
                    }
                    $jsonColumn->push($row);
                }
                $content->update(['file' => $jsonColumn]);
                Storage::disk('general')->append('refactorFileName.txt', $content->id);
            } catch (Exception $exception) {
                TempBucketLog::create([
                    'cat' => 7, 'file_url' => $file,
                    'error_detail' => 'error when refactor fileName in table, code='.$exception->getCode().', message='.$exception->getMessage()
                ]);
            }
        }
    }

    public function handle22()
    {
        $fromBucket = 'testBucket';

        $oldFiles = Storage::disk($fromBucket)->allFiles('420');
        foreach ($oldFiles as $oldFile) {

        }
    }

    public function handle23()
    {
        $ensaniPackages = [
            Product::RAHE_ABRISHAM1401_PACK_OMOOMI, Product::HAMAYESH_BUNDLES_ENSANI, 834,
        ];
        $users = User::whereHas('roles', function ($query) {
            return $query->where('id', 123);
        })
            ->where('major_id', '=', 3)
            ->where(function ($query) use ($ensaniPackages) {
                return $query->whereDoesntHave('orderproducts', function ($q) use ($ensaniPackages) {
                    return $q->where('product_id', $ensaniPackages[0]);
                })->orWhereDoesntHave('orderproducts', function ($q) use ($ensaniPackages) {
                    return $q->where('product_id', $ensaniPackages[1]);
                })->orWhereDoesntHave('orderproducts', function ($q) use ($ensaniPackages) {
                    return $q->where('product_id', $ensaniPackages[2]);
                });
            })
            ->get();
        $count = $users->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($users as $user) {
            event(new BonyadEhsanUserUpdate($user));
            $bar->advance();
        }
        $bar->finish();
        $this->info('done');
        return 0;
    }

    //refactor dana_content_transfers table

    public function handle24()
    {
        $serviceIds = [
            1 => 1,
            2 => 1,
            3 => 1,
            4 => 1,
            5 => 1,
            6 => 1,
            7 => 1,
            8 => 1,
            9 => 1,
            10 => 1,
            11 => 1,
            12 => 1,
            13 => 1,
            14 => 1,
            15 => 1,
            16 => 1,
            17 => 1,
            18 => 1,
            19 => 1,
            20 => 1,
            21 => 1,
            22 => 1,
            23 => 1,
            24 => 1,
            25 => 1,
            26 => 1,
            27 => 1,
            28 => 1,
            29 => 1,
            30 => 1,
            31 => 1,
            32 => 1,
            33 => 1,
            34 => 1,
            35 => 1,
            36 => 1,
            37 => 1,
            38 => 1,
            39 => 1,
            40 => 1,
            41 => 1,
            42 => 1,
            43 => 1,
            44 => 1,
            45 => 1,
            46 => 1,
            47 => 1,
            48 => 1,
            49 => 1,
            50 => 1,
            51 => 1,
            52 => 1,
            53 => 1,
            54 => 1,
            55 => 1,
            56 => 1,
            57 => 1,
            58 => 1,
            59 => 1,
            60 => 1,
            61 => 1,
            62 => 1,
            63 => 1,
            64 => 1,
            65 => 1,
            66 => 1,
            67 => 1,
            68 => 1,
            69 => 1,
            70 => 1,
            71 => 1,
            72 => 1,
            73 => 1,
            74 => 1,
            75 => 1,
            76 => 1,
            77 => 1,
            78 => 1,
            79 => 1,
            80 => 1,
            81 => 1,
            82 => 1,
            83 => 1,
            84 => 1,
            85 => 1,
            86 => 1,
            87 => 1,
            88 => 1,
            89 => 1,
            90 => 1,
            91 => 1,
            92 => 1,
            93 => 1,
            94 => 1,
            95 => 1,
            96 => 1,
            97 => 1,
            98 => 1,
            99 => 1,
            100 => 1,
            101 => 1,
            102 => 1,
            103 => 1,
            104 => 1,
            105 => 1,
            106 => 1,
            107 => 1,
            108 => 1,
            109 => 1,
            110 => 1,
            111 => 1,
            112 => 1,
            113 => 1,
            114 => 1,
            115 => 1,
            116 => 1,
            117 => 1,
            118 => 1,
            119 => 1,
            120 => 1,
            121 => 1,
            122 => 1,
            123 => 1,
            124 => 1,
            125 => 1,
            126 => 1,
            127 => 1,
            128 => 1,
            129 => 1,
            130 => 1,
            131 => 1,
            134 => 1,
            135 => 1,
            136 => 1,
            140 => 1,
            144 => 1,
            146 => 1,
            148 => 1,
            152 => 1,
            154 => 1,
            156 => 1,
            158 => 1,
            162 => 1,
            167 => 1,
            171 => 1,
            175 => 1,
            179 => 1,
            180 => 1,
            182 => 1,
            186 => 1,
            188 => 1,
            190 => 1,
            191 => 1,
            192 => 1,
            194 => 1,
            195 => 1,
            197 => 1,
            199 => 1,
            201 => 1,
            203 => 1,
            205 => 1,
            207 => 1,
            209 => 1,
            214 => 1,
            219 => 1,
            221 => 1,
            223 => 1,
            224 => 1,
            225 => 1,
            226 => 1,
            227 => 1,
            228 => 1,
            229 => 1,
            230 => 1,
            231 => 1,
            232 => 1,
            233 => 1,
            234 => 1,
            235 => 1,
            236 => 1,
            237 => 1,
            238 => 1,
            239 => 1,
            240 => 1,
            241 => 1,
            242 => 1,
            243 => 1,
            244 => 1,
            245 => 1,
            246 => 1,
            247 => 1,
            248 => 1,
            249 => 1,
            250 => 1,
            251 => 1,
            252 => 1,
            253 => 1,
            254 => 1,
            255 => 1,
            256 => 1,
            257 => 1,
            258 => 1,
            259 => 1,
            260 => 1,
            261 => 1,
            262 => 1,
            263 => 1,
            264 => 1,
            265 => 1,
            266 => 1,
            267 => 1,
            268 => 1,
            269 => 1,
            270 => 1,
            272 => 1,
            273 => 1,
            274 => 1,
            275 => 1,
            276 => 1,
            277 => 1,
            278 => 1,
            279 => 1,
            280 => 1,
            281 => 1,
            282 => 1,
            283 => 1,
            284 => 1,
            285 => 1,
            286 => 1,
            287 => 1,
            288 => 1,
            289 => 1,
            290 => 1,
            291 => 1,
            292 => 1,
            293 => 1,
            294 => 1,
            295 => 1,
            296 => 1,
            297 => 1,
            298 => 1,
            299 => 1,
            300 => 1,
            301 => 1,
            302 => 1,
            303 => 1,
            304 => 1,
            305 => 1,
            306 => 1,
            307 => 1,
            308 => 1,
            309 => 1,
            310 => 1,
            311 => 1,
            312 => 1,
            313 => 1,
            315 => 1,
            316 => 1,
            317 => 1,
            318 => 1,
            319 => 1,
            320 => 1,
            321 => 1,
            323 => 1,
            324 => 1,
            325 => 1,
            326 => 1,
            327 => 1,
            328 => 1,
            329 => 1,
            330 => 1,
            332 => 1,
            333 => 1,
            334 => 1,
            335 => 1,
            336 => 1,
            337 => 1,
            338 => 1,
            339 => 1,
            340 => 2,
            341 => 2,
            342 => 2,
            343 => 2,
            344 => 2,
            345 => 2,
            346 => 2,
            347 => 2,
            348 => 2,
            349 => 2,
            350 => 2,
            351 => 2,
            352 => 2,
            353 => 2,
            354 => 2,
            355 => 2,
            356 => 2,
            357 => 2,
            358 => 2,
            359 => 2,
            360 => 2,
            361 => 2,
            362 => 2,
            363 => 2,
            364 => 2,
            365 => 2,
            366 => 2,
            367 => 2,
            368 => 2,
            369 => 2,
            370 => 2,
            371 => 2,
            372 => 2,
            373 => 2,
            374 => 2,
            375 => 2,
            376 => 2,
            377 => 2,
            378 => 2,
            379 => 2,
            380 => 2,
            381 => 2,
            382 => 2,
            383 => 2,
            384 => 2,
            385 => 2,
            386 => 2,
            387 => 2,
            388 => 2,
            389 => 2,
            390 => 2,
            391 => 1,
            392 => 1,
            393 => 1,
            394 => 1,
            395 => 1,
            396 => 1,
            398 => 1,
            399 => 1,
            401 => 1,
            402 => 1,
            403 => 1,
            404 => 1,
            405 => 1,
            406 => 2,
            407 => 1,
            408 => 1,
            409 => 1,
            410 => 1,
            411 => 1,
            412 => 1,
            413 => 1,
            414 => 2,
            415 => 1,
            416 => 2,
            417 => 2,
            418 => 2,
            419 => 1,
            420 => 1,
            421 => 1,
            422 => 1,
            423 => 1,
            424 => 1,
            425 => 1,
            426 => 1,
            427 => 1,
            428 => 1,
            429 => 1,
            430 => 1,
            431 => 1,
            432 => 1,
            433 => 1,
            434 => 1,
            435 => 1,
            436 => 1,
            437 => 1,
            438 => 2,
        ];
        $count = count($serviceIds);
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($serviceIds as $key => $serviceId) {
            try {
                Permission::where('id', $key)->update(['service_id' => $serviceId]);
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('done');
        return 0;

    }

    //correct dana_product_content_transfers table

    public function handle25()
    {
        $this->info('Exporting excel');
        $productIds = [756, 757, 758];
        $time = Carbon::now()->toDateTimeString();
        $orders = Order::
        whereOrderstatusId(config('constants.ORDER_STATUS_CLOSED'))
            ->whereIn('paymentstatus_id',
                [config('constants.PAYMENT_STATUS_INDEBTED'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
            ->whereHas('orderproducts', function ($query) use ($productIds) {
                return $query->whereIn('product_id', $productIds);
            })
            ->whereHas('transactions', function ($query) use ($time) {
                return $query->whereTransactionstatusId(config('constants.TRANSACTION_STATUS_UNPAID'))
                    ->where(function ($q) use ($time) {
                        return $q->where('deadline_at', '<=', $time)
                            ->orWhere('deadline_at', null);
                    })
                    ->where('completed_at', null);
            })
            ->with([
                'transactions' => function ($query) use ($time) {
                    return $query->whereTransactionstatusId(config('constants.TRANSACTION_STATUS_UNPAID'))
                        ->where(function ($q) use ($time) {
                            return $q
                                ->where('deadline_at', '<=', $time)
                                ->orWhere('deadline_at', null);
                        })
                        ->where('completed_at', null);
                },
                'user',
            ])
            ->get();

        if (!$this->confirm("{$orders->count()} found")) {
            return false;
        }

        $data = [];
        foreach ($orders as $order) {
            $key = array_search($order->user_id, array_column($data, 'userId'));
            if ($key === false) {
                $data[] = [
                    'userId' => $order->user_id,
                    'fullName' => $order->user->full_name,
                    'orderDate' => $order->convertDate($order->completed_at, 'toJalali'),
                    'mobile' => $order->user->mobile,
                    'products' => implode(' , ',
                        $order->normalOrderproducts->whereIn('product_id', $productIds)->map(function ($orderproduct) {
                            return $orderproduct->product->name;
                        })->flatten()->toArray()),
                    'instalmentNum' => $order->transactions->count(),
                    'instalmentSum' => array_sum($order->transactions->pluck('cost')->toArray()),
                ];
                continue;
            }
            $data[$key]['orderDate'] =
                $data[$key]['orderDate'].' , '.$order->convertDate($order->completed_at, 'toJalali');
            $data[$key]['products'] =
                $data[$key]['products'].' , '.implode(' , ',
                    $order->normalOrderproducts->whereIn('product_id', $productIds)->map(function ($orderproduct) {
                        return $orderproduct->product->name;
                    })->flatten()->toArray());
            $data[$key]['instalmentNum'] = $data[$key]['instalmentNum'] + $order->transactions->count();
            $data[$key]['instalmentSum'] =
                $data[$key]['instalmentSum'] + array_sum($order->transactions->pluck('cost')->toArray());

        }

        $disk = config('disks.GENERAL');
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_users_with_Deferred installments_$now.xlsx";
        $headers =
            [
                'شناسه کاربری', 'نام و نام خانوادگی', 'تاریخ ثبت سفارش', 'شماره موبایل', 'عنوان محصولات خریداری شده',
                'تعداد اقساط معوق', 'مجموع بدهی'
            ];
        Excel::store(new DefaultClassExport(collect($data), $headers), $fileName, $disk);
    }

    //fill column product in dana_product_set_transfers table

    public function handle26()
    {
        $this->info('Fixing products');
        $productIds = [919, 925, 931, 937, 943, 803, 818, 833, 875, 889, 895, 901, 907, 913];
        $products = Product::with('grandsChildren.grandsChildren')->whereIn('id', $productIds)->get();


        $count = $products->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }

        $bar = $this->output->createProgressBar($count);

        DB::transaction(function () use ($products, $bar) {
            $products->each(function ($product) use ($bar) {
                $image = $product->image;
                $grandChildren = $product->grandsChildren()->first();
                $grandChildren->image = $image;
                $grandChildren->save();
                $grandChildren->grandsChildren()->update(['image' => $image]);
                $bar->advance();
            });
        });


        $bar->finish();
        $this->info("\n");
        $this->info('done');
        return 0;
    }

    //delete rows form dana that not exist in dana

    public function handle27()
    {
        $favoredContents = collect();
        DB::table('favorables')
            ->where('favorable_type', 'App\Content')
            ->orderBy('created_at')
            ->chunk(500, function ($favorites) use ($favoredContents) {
                foreach ($favorites as $favorite) {
                    $favoredContents->push($favorite);
                }
            });
        DB::table('favorables')
            ->where('favorable_type', 'App\Timepoint')
            ->orderBy('created_at')
            ->chunk(500, function ($favorites) use ($favoredContents) {
                $data = [];
                foreach ($favorites as $favorite) {
                    $timePoint = Timepoint::where('id', $favorite->favorable_id)->first();
                    if (
                        !is_null($timePoint)
                        && !$favoredContents->contains(function ($value) use ($favorite, $timePoint) {
                            return $value->user_id == $favorite->user_id && $value->favorable_id == $timePoint->content_id;
                        })
                    ) {
                        $data[] = [
                            'user_id' => $favorite->user_id,
                            'favorable_id' => $timePoint->content_id,
                            'favorable_type' => 'App\Content',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                DB::table('favorables')->insert($data);
            });
        return 0;
    }

    //Fixing teachers of sets

    public function handle29()
    {
        $headers = ['تاریخ', 'شماره پرسنلی', 'ورود', 'خروج'];
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_time_sheet_{$now}.xlsx";
        $disk = config('disks.GENERAL');
        $table = $this->generateTableOfTimeSheets();
        Excel::store(new DefaultClassExport($table, $headers), $fileName, $disk);

        $this->info('Done');
        return 0;

    }

    //delete rows form dana that not exist in dana

    private function generateTableOfTimeSheets(): Collection
    {
        $rows = Employeetimesheet::all();

        $bar = $this->output->createProgressBar($rows->count());
        $table = collect();
        foreach ($rows as $row) {
            $this->info('Processing row '.$row['year']);
            $rowCollection = collect();

            $column1 = 0;

            $yearUsers = User::where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end']);

            $column2 = $yearUsers->count();

            $column3 = $yearUsers->whereHas('orders', function ($q) use ($row) {
                $q->whereHas('transactions', function ($q2) use ($row) {
                    $q2->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                        ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                        ->whereNull('wallet_id')
                        ->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end']);
                });
            })->count();

            $column4 = Transaction::query()
                ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                ->whereNull('wallet_id')
                ->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end'])
                ->whereHas('order', function ($q) use ($row) {
                    $q->whereHas('user', function ($q2) use ($row) {
                        $q2->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end']);
                    });
                })->sum('cost');

            $column5 = User::where('created_at', '<', $row['begin'])
                ->whereHas('orders', function ($q) use ($row) {
                    $q->whereHas('transactions', function ($q2) use ($row) {
                        $q2->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                            ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                            ->whereNull('wallet_id')
                            ->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end']);
                    });
                })->count();

            $column6 = Transaction::query()
                ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                ->whereNull('wallet_id')
                ->where('created_at', '>=', $row['begin'])->where('created_at', '<', $row['end'])
                ->whereHas('order', function ($q) use ($row) {
                    $q->whereHas('user', function ($q2) use ($row) {
                        $q2->where('created_at', '<', $row['begin']);
                    });
                })->sum('cost');

            $rowCollection->push([$row['year'], $column1, $column2, $column3, $column4, $column5, $column6]);

            $table->push($rowCollection);
            $bar->advance();
            $this->info("\n");
        }

        $bar->finish();
        $this->info("\n");
        return $table;
    }

    //calculate cost for null cost orders

    public function handle30()
    {
        $this->info('Running filling DanaContentTransfer command');
        $danaContentTransfers = DanaContentTransfer::all();
        $count = count($danaContentTransfers);
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($danaContentTransfers as $danaContentTransfer) {
            $content = Content::find($danaContentTransfer->educationalcontent_id);
            $danaCourse = DanaSetTransfer::where('contentset_id', $content->contentset_id)->first();
            if (!isset($danaCourse)) {
                $this->info($content->id.' does not have dana course');
                $bar->advance();
                continue;
            }
            $danaCourseId = $danaCourse->dana_course_id;
            $danaContentId =
                DanaService::getDanaSessionContentIdByFileManagerId($danaCourseId,
                    $danaContentTransfer->dana_session_id, $danaContentTransfer->dana_filemanager_content_id);
            if (is_null($danaContentId)) {
                Log::channel('debug')->alert('can not update id='.$danaContentTransfer->id.' in dana_content_transfers_table');
                $this->error('can not update id='.$danaContentTransfer->id.' in dana_content_transfers_table');
                $bar->advance();
                continue;
            }
            $danaContentTransfer->update([
                'dana_course_id' => $danaCourseId,
                'dana_content_id' => $danaContentId,
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->info('done');
    }

    public function handle31()
    {
        $this->info('Running filling DanaProductContentTransfer command');
        $danaProductContentTransfers =
            DanaProductContentTransfer::where('dana_filemanager_content_id', '<>', null)->where('dana_content_id',
                null)->get();
        $count = $danaProductContentTransfers->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $bar = $this->output->createProgressBar($count);

        foreach ($danaProductContentTransfers as $danaProductContentTransfer) {
            $danaProductContentTransfer->update([
                'dana_content_id' => DanaProductService::getDanaSessionContentIdByFileManagerId(
                    $danaProductContentTransfer->dana_course_id,
                    $danaProductContentTransfer->dana_session_id,
                    $danaProductContentTransfer->dana_filemanager_content_id,
                ),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->info('done');
    }

    public function handle32()
    {
        $danaProductSets = DanaProductSetTransfer::all();
        $count = $danaProductSets->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($danaProductSets as $danaProductSet) {
            $contentSet = Contentset::find($danaProductSet->contentset_id);
            $contentSetProducts = $contentSet->products;
            if ($contentSetProducts->count() > 1) {
                $this->error('contentset='.$contentSet->id.' has more than one products');
                $bar->advance();
                continue;
            }
            $danaProductSet->update([
                'product_id' => $contentSetProducts->first()->id,
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->info('done');
    }

    public function handle33()
    {
        $courseId = 1291;
        $productId = 981;
        $courseSessions = DanaProductService::getDanaSession($courseId);
        unset($courseSessions['status_code']);
        $existSessionId = [];
        foreach ($courseSessions as $courseSession) {
            $existSessionId[] = $courseSession['sessionID'];
        }

        $notExistSessions =
            DanaProductSetTransfer::whereNotIn('dana_session_id', $existSessionId)->where('product_id',
                $productId)->get();

        $count = count($notExistSessions);
        if (!$this->confirm("{$count} rows found , continue?")) {
            return false;
        }

        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();
        foreach ($notExistSessions as $notExistSession) {
            DanaProductContentTransfer::where('dana_course_id', $courseId)->where('dana_session_id',
                $notExistSession->dana_session_id)->delete();
            $notExistSession->delete();
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->info('done');
    }

    public function handle34()
    {
        $this->info('Fixing authors of contentsets');
        $sets = Contentset::query()->where('author_id', 1)->get();


        $count = $sets->count();
        if (!$this->confirm("{$count} found, continue?")) {
            return false;
        }

        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();

        foreach ($sets as $set) {
            $author = $set?->user;

            if (!isset($author)) {
                $this->info('no teacher found for set-'.$set->id);
                $progressBar->advance();
                continue;
            }

            $allContents = $set->contents->whereNotNull('author_id');
            if ($allContents->isEmpty()) {
                $this->info("set-{$set->id} has no contents");
                $progressBar->advance();
                continue;
            }
            if (isset($author) && $author->id == 1) {
                $firstContent =
                    $allContents->where('contenttype_id',
                        config('constants.CONTENT_TYPE_VIDEO'))->sortBy('order')->first();
                $author = $firstContent?->user;
            }
            if (isset($author) && $author->id == 1) {
                $firstContent =
                    $allContents->where('contenttype_id',
                        config('constants.CONTENT_TYPE_PAMPHLET'))->sortBy('order')->first();
                $author = $firstContent?->user;
            }

            if (!isset($author) || $author->id == 1) {
                $this->info('no other teacher found for set-'.$set->id);
                $progressBar->advance();
                continue;
            }

            $set->update(['author_id' => $author->id]);

            $progressBar->advance();
        }

        $this->info('Done!');
        $progressBar->finish();
        return true;
    }

    public function handle35()
    {
        $courseId = 1291;
        $productId = 981;
        $courseSessions = DanaProductService::getDanaSession($courseId);
        unset($courseSessions['status_code']);
        $existSessionId = [];
        foreach ($courseSessions as $courseSession) {
            $existSessionId[] = $courseSession['sessionID'];
        }

        $notExistSessions =
            DanaProductSetTransfer::whereNotIn('dana_session_id', $existSessionId)->where('product_id',
                $productId)->get();

        $count = count($notExistSessions);
        if (!$this->confirm("{$count} rows found , continue?")) {
            return false;
        }

        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();
        foreach ($notExistSessions as $notExistSession) {
            DanaProductContentTransfer::where('dana_course_id', $courseId)->where('dana_session_id',
                $notExistSession->dana_session_id)->delete();
            $notExistSession->delete();
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->info('done');
    }

    public function handle36()
    {
        $orders = Orderproduct::query()
            ->whereHas('order', function ($q0) {
                $q0->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                    ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'))
                    ->whereHas('transactions', function ($q) {
                        $q->where('transactionstatus_id',
                            config('constants.TRANSACTION_STATUS_SUCCESSFUL'))->whereIn('paymentmethod_id',
                            [config('constants.PAYMENT_METHOD_ATM'), config('constants.PAYMENT_METHOD_ONLINE')]);
                    });
            })
            ->whereHas('product', function ($q) {
                $q->where('seller', 2);
            })->get();
        $count = count($orders);
        if (!$this->confirm("{$count} rows found , continue?")) {
            return false;
        }
        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();
        $totalIncome = 0;
        foreach ($orders as $order) {
            $totalIncome += $order->whereHas('successfulTransactions', function ($query) {
                $query->whereIn('paymentmethod_id', [1, 2])
                    ->whereNull('wallet_id');
            });
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->info('done');
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function handle37(AlaaStatistics $alaaStatistics): int
    {
        $this->info('Generating monthly report');
        $alaaStatistics->get();
        return 0;
    }

    public function handle38()
    {
        $orderProductsBuilder = Orderproduct::where('orderproducttype_id', 1)
            ->whereIn('product_id',
                [1067, 1068, 1069, 1070, 1071, 1072, 1073, 1077, 1078, 1079, 1080, 1081, 1082, 1083, 1084])
            ->whereHas('order', function ($query) {
                $query->paidAndClosed()->whereHas('orderproducts', function ($query) {
                    $query->whereIn('product_id', [1065, 1066]);
                });
            });
        Billing::whereIn('op_id', $orderProductsBuilder->pluck('id'))->delete();
        $orderProductsBuilder->update([
            'orderproducttype_id' => 3,
        ]);
    }

    public function handle39()
    {
        $wallets = Wallet::where('balance', '!=', 0)->where('wallettype_id', 2)->get();
        foreach ($wallets as $wallet) {
            $wallet->update(['pending_to_reduce' => 0]);
            $wallet->withdraw($wallet->balance, description: 'پایان سال تحصیلی');
        }
    }

    //Registering users to 3a exams

    public function handle40()
    {
        $product = Product::find(956);
        /** @var Product $product */
        if (!isset($product)) {
            $this->error("couldn't find product");
            return 0;
        }
        if ($response = $product->validateProduct()) {
            $this->error($response);
            return 0;
        }
        if (!$product->isFree()) {
            $this->error('subscription is not free');
            return 0;
        }
        $features = $product->attributevaluesByType(config('constants.ATTRIBUTE_TYPE_SUBSCRIPTION'))
            ->load('attribute')
            ->pluck('name', 'attribute.name');

        $features->each(function ($value, $key) use (&$values) {
            $values[] = [
                'title' => $key,
                'usageLimit' => $value,
                'usage' => $value == config('constants.ATTRIBUTE_VALUE_INFINITE') ? config('constants.ATTRIBUTE_VALUE_INFINITE') : 0,
            ];
        });
        $data = [
            ['09942018996', '4580505913'],
            ['09194239142', '0441296939'],
            ['09178753972', '3550176325'],
            ['09222418591', '2981510258'],
            ['09220854675', '6600143551'],
            ['09109755073', '0441182593'],
            ['09175882864', '6790104135'],
            ['09038081021', '4880262846'],
            ['09928447734', '4221144106'],
            ['09364850094', '2550278747'],
            ['09905993721', '2560585782'],
            ['09134400731', '2981566253'],
            ['09901672617', '2219752161'],
            ['09022349171', '5410304756'],
            ['09303880471', '1810816475'],
            ['09905915704', '0927200740'],
        ];
        foreach ($data as $datum) {
            $mobile = $datum[0];
            $nationalCode = $datum[1];
            $user = User::where('mobile', $mobile)->where('nationalCode', $nationalCode)->first();
            if (!isset($user)) {
                if (nationalCodeValidation($nationalCode)) {
                    if (mobileValidation($mobile)) {
                        $user = User::create([
                            'mobile' => $mobile,
                            'nationalCode' => $nationalCode,
                            'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                            'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                            'password' => bcrypt($nationalCode),
                        ]);
                    } else {
                        Log::channel('debug')->debug("mobile is not valid , mobile : $mobile, national code : $nationalCode");
                        continue;
                    }
                } else {
                    Log::channel('debug')->debug("national code is not valid , mobile : $mobile, national code : $nationalCode");
                    continue;
                }
            }
            Cache::tags(['userAsset', 'userAsset_'.$user->id])->flush();
            if ($user->userHasAnyOfTheseProducts2([$product->id])) {
                Log::channel('debug')->debug("user_id : $user->id had subscription before");
                continue;
            }
            try {
                DB::beginTransaction();
                $order =
                    OrderRepo::createBasicCompletedOrder($user->id, config('constants.PAYMENT_STATUS_PAID'), 0, 0,
                        seller: config('constants.SOALAA_SELLER'));
                OrderproductRepo::createBasicOrderproduct($order->id, $product->id, 0, 0);
                $user->subscribedProducts()->attach($product->id, [
                    'order_id' => $order->id,
                    'seller' => $product->seller,
                    'values' => json_encode($values),
                    'valid_since' => Carbon::now(),
                    'valid_until' => isset($features['duration']) ? Carbon::now()->addDays((int) $features['duration']) : Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);
                DB::commit();
            } catch (Exception $exception) {
                DB::rollBack();
                Log::channel('debug')->debug("error occurred while adding subscription for user_id : $user->id");
            }
        }
        return 0;
    }

    public function handle41()
    {
        $details = [
            1071 => 'Arash1403_shimi2#14020407',
            1084 => 'Arash1403_zamin1#14020407',
            1083 => 'Arash1403_zamin2#14020407',
            1078 => 'Arash1403_zist#14020407',
            981 => 'chatr1402_hendeseh#14020407',
            980 => 'chatr1402_hesaban#14020407',
            978 => 'chatr1402_physic#14020407',
            967 => 'chatr1402_priyazi#14020407',
            968 => 'chatr1402_ptajtobi#14020407',
            975 => 'chatr1402_Riyaziattajrobi#14020407',
            976 => 'chatr1402_shimi#14020407',
            974 => 'chatr1402_zist#14020407',
            792 => 'foriat1101402_adabiattakhasosi#14020407',
            800 => 'foriat1101402_arabitakhasosi#14020407',
            797 => 'foriat1101402_eghtesad#14020407',
            791 => 'foriat1101402_falsafeh#14020407',
            782 => 'foriat1101402_hendeseh#14020407',
            781 => 'foriat1101402_hesaban#14020407',
            798 => 'foriat1101402_jamee#14020407',
            796 => 'foriat1101402_ravan#14020407',
            787 => 'foriat1101402_riyaziattajrobi#14020407',
            790 => 'foriat1101402_riyaziyatensani#14020407',
            784 => 'foriat1101402_Rphysic#14020407',
            963 => 'foriat1101402_shimi#14020407',
            951 => 'foriat1101402_tarikhjoghrariya#14020407',
            785 => 'foriat1101402_Tphysic#14020407',
            789 => 'foriat1101402_zamin#14020407',
            788 => 'foriat1101402_zist#14020407',
            440 => 'Rah1402_physic1#14020407',
            441 => 'Rah1402_physic2#14020407',
            446 => 'Rah1402_priyazi#14020407',
            445 => 'Rah1402_ptajrobi#14020407',
            439 => 'Rah1402_riyaziatriyazi#14020407',
            347 => 'Rah1402_riyaziattajrobi#14020407',
            443 => 'Rah1402_shimi#14020407',
            442 => 'Rah1402_zist#14020407',
            1099 => 'raheAbrisham1403_gosasteh#14020407',
            1091 => 'raheAbrisham1403_hendeseh#14020407',
            1090 => 'raheAbrisham1403_hesaban1#14020407',
            1101 => 'raheAbrisham1403_hesaban2#14020407',
            1094 => 'raheAbrisham1403_physic#14020407',
            1097 => 'raheAbrisham1403_priyazi#14020407',
            1096 => 'raheAbrisham1403_ptajrobi#14020407',
            1092 => 'raheAbrisham1403_riyaziyattajrobi1#14020407',
            1100 => 'raheAbrisham1403_riyaziyattajrobi2#14020407',
            1095 => 'raheAbrisham1403_shimi#14020407',
            1093 => 'raheAbrisham1403_zist#14020407',
            1009 => 'shelik1402_priyazi#14020407',
            1008 => 'shelik1402_ptajrobi#14020407',
            628 => 'teta1402_dastorzaban#14020407',
            600 => 'teta1402_halmasaelshimi#14020407',
            683 => 'teta1402_hefziatshimi#14020407',
            421 => 'teta1402_zistgiyahi#14020407',
            545 => 'titan1402_gosasteh#14020407',
            544 => 'titan1402_hendeseh#14020407',
            714 => 'titan1402_hesaban#14020407',
            713 => 'titan1402_riyaziattajtobi#14020407',
            535 => 'titan1402_shimi#14020407',
            712 => 'titan1402_zist#14020407',
            1082 => 'Arash1403_gosasteh#14020407',
            1081 => 'Arash1403_hendeseh#14020407',
            1077 => 'Arash1403_hesaban#14020407',
            1070 => 'Arash1403_physic1#14020407',
            1079 => 'Arash1403_physic2#14020407',
            1066 => 'Arash1403_priyazi#14020407',
            1065 => 'Arash1403_ptajrobi#14020407',
            1067 => 'Arash1403_riyaziyattajrobi#14020407',
            1080 => 'Arash1403_shimi1#14020407',
        ];
        $vouchers = Productvoucher::pluck('code')->toArray();

        $count = count($details);
        if (!$this->confirm("$count ? ")) {
            return false;
        }

        $bar = $this->output->createProgressBar($count);
        foreach ($details as $productId => $packageName) {
            for ($i = 0; $i < 50; $i++) {
                if (empty($vouchers)) {
                    $voucher = 'h-'.rand(1000, 9999);
                } else {
                    do {
                        $voucher = 'h-'.rand(1000, 9999);
                    } while (in_array($voucher, $vouchers));
                }
                $vouchers[] = $voucher;
                ProductvoucherRepo::create([
                    'contractor_id' => 2,
                    'products' => [$productId],
                    'expirationdatetime' => '2024-07-21',
                    'package_name' => $packageName,
                    'coupon_id' => 6474,
                    'code' => $voucher,
                    'description' => route('web.voucher.submit', ['code' => $voucher]),
                ]);
            }
            $bar->advance();
        }

        $bar->finish();
        return 0;
    }

    public function handle42()
    {
        $since = '2023-06-23 00:00:00';
        $till = '2023-08-08 00:00:00';
        $sum = Transaction::where('completed_at', '>=', $since)
            ->where('completed_at', '<', $till)
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
            ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
            ->where('cost', '>', 0)
            ->sum('cost');

        $this->info(number_format($sum));
    }

    public function handle43()
    {
        $duplicatedCodes = DB::table('referral_codes')
            ->select('code', DB::raw('COUNT(*) as `count`'))
            ->groupBy('code')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $count = $duplicatedCodes->count();
        if (!$this->confirm("{$count} found, continue?")) {
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        foreach ($duplicatedCodes as $code) {
            $duplicatedReferralCodes = ReferralCode::where('code', $code->code)->orderBy('usageNumber', 'desc')->get();
            foreach ($duplicatedReferralCodes as $index => $referralCode) {
                if ($index === 0) {
                    if ($referralCode->usageNumber != 0) {
                        Log::channel('debug')->warning("Referral code was used with code = $referralCode->code and duplicated referral codes were changed");
                    }
                    continue;
                }
                $referralCode->update([
                    'code' => $this->generateCode(),
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('Done!');
        return 0;
    }

    public function handle44()
    {
        $abrisham2ProductIds = array_keys(Product::ABRISHAM_2_DATA);
        $users = User::with([
            'watchContents' => function ($query) use ($abrisham2ProductIds) {
                $query->whereHas('set.products', function ($query) use ($abrisham2ProductIds) {
                    $query->whereIn('id', $abrisham2ProductIds);
                });
            },
        ])->whereHas('orders', function ($query) use ($abrisham2ProductIds) {
            $query->paidAndClosed()->whereHas('orderproducts', function ($query) use ($abrisham2ProductIds) {
                $query->whereIn('product_id', $abrisham2ProductIds);
            });
        })->get();

        $count = $users->count();
        if (!$this->confirm("{$count} found, continue?")) {
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        foreach ($users as $user) {
            $studyEvent = $user->getActiveStudyEvents()->first();
            foreach ($user->watchContents as $content) {
                $content->watches()->update([
                    'studyevent_id' => isset($studyEvent) ? $studyEvent->id : 13,
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('Done!');
        return 0;
    }

    public function handle45()
    {
        $referralCodes = ReferralCode::with([
            'orders' => function ($query) {
                $query->paidAndClosed();
            },
        ])->sold()->used(1)->whereNull('used_at')->get();
        $count = $referralCodes->count();
        if (!$this->confirm("{$count} found, continue?")) {
            return 0;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($referralCodes as $referralCode) {
            $referralCode->update([
                'used_at' => $referralCode->orders->first()?->completed_at,
            ]);
            Log::channel('debug')->warning("Referral code {$referralCode->id} does not have any orders");
            $bar->advance();
        }
        $bar->finish();
        $this->info('Done!');
        return 0;
    }

    public function handle46()
    {
        $this->info('registering abrisham2 students in  3a exams');
        $cost1214 = 1800000;
        $cost1196 = 1800000;
        $cost1178 = 1800000;
        $examAbrishamMap = [
            1101 => [1214], // all riyazi
            1099 => [1214], // all riyazi
            1097 => [1214, 1196], // all riyazi
            1095 => [1214, 1196], // all riyazi
            1094 => [1214], // all riyazi
            1091 => [1214], // all riyazi
            1090 => [1214], // all riyazi
            1092 => [1196], // all tajrobi
            1093 => [1196], // all tajrobi
            1096 => [1196], // all tajrobi
            1100 => [1196], // all tajrobi
            1098 => [1178], // all ensani
        ];
        $examProductCosts = [
            1214 => $cost1214,
            1196 => $cost1196,
            1178 => $cost1178,
        ];
        $abrisham2Products = [1090, 1091, 1092, 1093, 1094, 1095, 1096, 1097, 1098, 1099, 1100, 1101];
        $orders = Order::with('orderproducts')->whereIn('paymentstatus_id', [
            config('constants.PAYMENT_STATUS_INDEBTED'),
            config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
            config('constants.PAYMENT_STATUS_PAID'),
            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
        ])->where('orderstatus_id', 2)
            ->whereHas('transactions', function ($q3) {
                $q3->where('cost', '>', 0)
                    ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                    ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'));
            })
            ->whereHas('orderproducts', function ($q2) use ($abrisham2Products) {
                $q2->whereIn('product_id', $abrisham2Products);
            })->get();

        $count = $orders->count();
        if (!$this->confirm("$count found, continue?")) {
            return 0;
        }

        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();

        foreach ($orders as $order) {
            $giftedExamsProductIds = [];
            $user = $order->user;
            if (!isset($user)) {
                $this->error("Order {$order->id} does not have user");
                $progressBar->advance();
                continue;
            }

            $abrisham2Orderproducts = $order->orderproducts->whereIn('product_id', $abrisham2Products);
            foreach ($abrisham2Orderproducts as $orderproduct) {
                $giftToGive = $examAbrishamMap[$orderproduct->product_id];
                foreach ($giftToGive as $item) {
                    if (!in_array($item, $giftedExamsProductIds)) {
                        $giftedExamsProductIds [] = $item;
                    }
                }
            }

            foreach ($giftedExamsProductIds as $examProductId) {
                OrderproductRepo::createGiftOrderproduct($order->id, $examProductId, $examProductCosts[$examProductId]);
            }

            $_3AOrderproducts = $order->orderproducts()->whereIn('product_id', array_keys($examProductCosts))->get();
            foreach ($_3AOrderproducts as $orderproduct) {
                $exams = _3aExam::productId($orderproduct->product_id)->get();

                foreach ($exams as $exam) {
                    $result = $this->register3ARequest($user, $exam->id);
                    if (!$result) {
                        Log::channel('debug')->error('Product '.$orderproduct->product_id.', Exam '.$exam->id.' was not registered for user '.$user->id);
                    }
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        Artisan::call('cache:clear');
        $this->info('Done!');
        return 1;
    }

    public function fixOrder1()
    {
        $orders = DB::table('orders')
            ->join('transactions', function ($join) {
                $join->on('orders.id', '=', 'transactions.order_id')
                    ->whereNull('transactions.deleted_at')
//                    ->where('transactiongateway_id' , 4)
                    ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'))
                    ->where('transactionstatus_id', 3);
            })
            ->where('orders.orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->whereIn('orders.paymentstatus_id',
                [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
//            ->where('orders.completed_at' , '>=' , '2022-01-22 00:00:00')
//            ->where('orders.completed_at' , '<' , '2022-02-22 00:00:00')
            ->orderBy('transactions.completed_at')
            ->select('orders.id as order_id', DB::raw('MIN(transactions.completed_at) as min_transaction_completed_at'))
            ->whereNull('orders.deleted_at')
            ->groupBy('orders.id')
            ->havingRaw('MAX(orders.completed_at) > min_transaction_completed_at');
        $orders = $orders->get();

        $count = $orders->count();
        if (!$this->confirm("$count ? ")) {
            return false;
        }


        $bar = $this->output->createProgressBar($count);
        foreach ($orders as $order) {
            [$orderId, $fixedCompetedAt] = [$order->order_id, $order->min_transaction_completed_at];
            DB::statement('UPDATE  `orders` SET `completed_at` = "'.$fixedCompetedAt.'" where `orders`.`id` = '.$orderId);
            Log::channel('debug')->debug('UPDATE  `orders` SET `completed_at` = "'.$fixedCompetedAt.'" where `orders`.`id` = '.$orderId);
            $bar->advance();
        }
        $bar->finish();
    }

    public function fixCoupon1()
    {
        $t = DB::table('orderproducts')
            ->join('orders', function ($join) {
                $join->on('orderproducts.order_id', '=', 'orders.id')
                    ->whereNull('orders.deleted_at');
            })
            ->join('coupons', function ($join) {
                $join->on('orders.coupon_id', '=', 'coupons.id')
                    ->whereNull('coupons.deleted_at')
                    ->where('coupons.coupontype_id', '=', 1);

            })
            ->where('includedInCoupon', '=', 0)
            ->select('orderproducts.id')
            ->distinct()
            ->whereNull('orderproducts.deleted_at')->get()->pluck('id')->toArray();
        $count = count($t);
        if (!$this->confirm("$count ? ")) {
            return false;
        }
        DB::statement('UPDATE orderproducts SET includedInCoupon = 1, tmp_share_order = null where id IN ('.implode(',',
                $t).')');
    }

    public function fixCoupon2()
    {
        $t = DB::table('coupons')
            ->join('coupon_product', function ($join) {
                $join->on('coupon_product.coupon_id', '=', 'coupons.id');
            })
            ->join('products', function ($join) {
                $join->on('products.id', '=', 'coupon_product.product_id')
                    ->whereNull('products.deleted_at');
            })
            ->join('orders', function ($join) {
                $join->on('orders.coupon_id', '=', 'coupons.id')
                    ->whereNull('orders.deleted_at');
            })
            ->join('orderproducts', function ($join) {
                $join->on('orderproducts.order_id', '=', 'orders.id')
                    ->whereNull('orderproducts.deleted_at');
            })
            ->whereNull('coupons.deleted_at')
            ->where('coupons.coupontype_id', '=', 2)
            ->where('orderproducts.includedInCoupon', '=', 0)
            ->where('order.id', '=', 10746)
            ->select('orderproducts.id as order_product_id', 'products.id as p_product_id',
                'orderproducts.product_id as op_product_id', 'coupons.id as coupon_id')
            ->distinct()
            ->get();

        $t = $t->filter(function ($item) {
            return $item->p_product_id == $item->op_product_id;
        });
        $count = count($t);
        if (!$this->confirm("$count ? ")) {
            return false;
        }
        $orderP = $t->pluck('order_product_id')->toArray();
        DB::statement('UPDATE orderproducts SET includedInCoupon = 1, tmp_share_order = null where id IN ('.implode(',',
                $orderP).')');
    }

    public function fixCoupon3()
    {
        $orders = Order::query()
            ->paidAndClosed()
            ->whereDoesntHave('orderproducts', function ($q) {
                $q->where('orderproducttype_id',
                    config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))->where('includedInCoupon', 1);
            })->where('cost', '>', 0)->get();

        $count = $orders->count();
        if (!$this->confirm("$count orders found , do you wish to continue?")) {
            return 0;
        }


        $bar = $this->output->createProgressBar($count);


        /** @var Order $order */
        foreach ($orders as $order) {
            $d = $order->costwithoutcoupon + $order->cost;
            $order->costwithoutcoupon = $d;
            $order->cost = 0;
            $order->updateWithoutTimestamp();
            $bar->advance();
        }
        $bar->finish();
    }

    public function fixOrder2()
    {
        $orders =
            DB::table('orders')
                ->join('orderproducts', function ($join) {
                    $join->on('orders.id', '=', 'orderproducts.order_id')
                        ->whereNull('orderproducts.deleted_at');
                })
                ->whereIn('orders.orderstatus_id',
                    [config('constants.ORDER_STATUS_CLOSED'), config('constants.ORDER_STATUS_POSTED')])
                ->whereIn('orders.paymentstatus_id',
                    [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
                ->whereNull('orders.deleted_at')
                ->select('orders.id as order_id',
                    DB::raw('SUM(orderproducts.tmp_final_cost) as op_final_cost'),
                    DB::raw('( orders.costwithoutcoupon + orders.cost ) as order_final_cost'),
                    'orders.discount as order_discount'
                )
                ->groupBy('orders.id', 'orders.costwithoutcoupon', 'orders.cost', 'orders.discount')
//                ->havingRaw('order_discount > order_final_cost')
                ->havingRaw('op_final_cost > order_final_cost')
                ->get();
        $count = $orders->count();
        if (!$this->confirm("$count ? ")) {
            return false;
        }
        foreach ($orders as $order) {
            [$orderId, $op_final_cost, $order_final_cost] =
                [$order->order_id, $order->op_final_cost, $order->order_final_cost];
            $diff = $op_final_cost - $order_final_cost;
            DB::statement('UPDATE  `orders` SET `discount` = discount + "'.$diff.'" ,costwithoutcoupon = costwithoutcoupon +"'.$diff.'" where `orders`.`id` = '.$orderId);
//            DB::statement('UPDATE  `orders` SET `discount` = 0 where `orders`.`id` = '. $orderId);
            DB::statement('UPDATE  `orderproducts` SET `tmp_share_order` = null where `order_id` = '.$orderId);
            Log::channel('debug')->debug('UPDATE  `orders` SET `discount` = "'.$diff.'" where `orders`.`id` = '.$orderId);
//            $bar->advance();
        }
    }

    public function fixOrder3()
    {
        $orders3 = Order::where('orderstatus_id', 2)
            ->where('couponDiscountAmount', '>', 0)
            ->get();

        $count = $orders3->count();
        if (!$this->confirm("$count orders found , do you wish to continue?")) {
            return 0;
        }


        $bar = $this->output->createProgressBar($count);

        foreach ($orders3 as $item) {
            $item->coupon_id = null;
            $item->discount = $item->couponDiscountAmount;
            $item->couponDiscountAmount = 0;

            $item->updateWithoutTimestamp();

            foreach ($item->orderproducts as $normalOrderproduct) {
                $normalOrderproduct->tmp_share_order = null;
                $normalOrderproduct->updateWithoutTimestamp();
            }
            $bar->advance();
        }

        $bar->finish();
    }
}
