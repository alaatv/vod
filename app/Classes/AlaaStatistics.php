<?php

namespace App\Classes;

use App\Exports\DefaultClassExport;
use App\Models\Content;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;

class AlaaStatistics
{
    public const CALENDER = [
        ['فروردین ۱۳۹۹', '2020-03-20', '2020-04-20'],
        ['اردیبهشت ۱۳۹۹', '2020-04-20', '2020-05-21'],
        ['خرداد ۱۳۹۹', '2020-05-21', '2020-06-21'],
        ['تیر ۱۳۹۹', '2020-06-21', '2020-07-22'],
        ['مرداد ۱۳۹۹', '2020-07-22', '2020-08-22'],
        ['شهریور ۱۳۹۹', '2020-08-22', '2020-09-22'],
        ['مهر ۱۳۹۹', '2020-09-22', '2020-10-22'],
        ['آبان ۱۳۹۹', '2020-10-22', '2020-11-21'],
        ['آذر ۱۳۹۹', '2020-11-21', '2020-12-21'],
        ['دی ۱۳۹۹', '2020-12-21', '2021-01-20'],
        ['بهمن ۱۳۹۹', '2021-01-20', '2021-02-19'],
        ['اسفند ۱۳۹۹', '2021-02-19', '2021-03-21'],
        ['فروردین ۱۴۰۰', '2021-03-21', '2021-04-21'],
        ['اردیبهشت ۱۴۰۰', '2021-04-21', '2021-05-22'],
        ['خرداد ۱۴۰۰', '2021-05-22', '2021-06-22'],
        ['تیر ۱۴۰۰', '2021-06-22', '2021-07-23'],
        ['مرداد ۱۴۰۰', '2021-07-23', '2021-08-23'],
        ['شهریور ۱۴۰۰', '2021-08-23', '2021-09-23'],
        ['مهر ۱۴۰۰', '2021-09-23', '2021-10-23'],
        ['آبان ۱۴۰۰', '2021-10-23', '2021-11-22'],
        ['آذر ۱۴۰۰', '2021-11-22', '2021-12-22'],
        ['دی ۱۴۰۰', '2021-12-22', '2022-01-21'],
        ['بهمن ۱۴۰۰', '2022-01-21', '2022-02-20'],
        ['اسفند ۱۴۰۰', '2022-02-20', '2022-03-21'],
        ['فروردین ۱۴۰۱', '2022-03-21', '2022-04-21'],
        ['اردیبهشت ۱۴۰۱', '2022-04-21', '2022-05-22'],
        ['خرداد ۱۴۰۱', '2022-05-22', '2022-06-22'],
        ['تیر ۱۴۰۱', '2022-06-22', '2022-07-23'],
        ['مرداد ۱۴۰۱', '2022-07-23', '2022-08-23'],
        ['شهریور ۱۴۰۱', '2022-08-23', '2022-09-23'],
        ['مهر ۱۴۰۱', '2022-09-23', '2022-10-23'],
        ['آبان ۱۴۰۱', '2022-10-23', '2022-11-22'],
        ['آذر ۱۴۰۱', '2022-11-22', '2022-12-22'],
        ['دی ۱۴۰۱', '2022-12-22', '2023-01-21'],
        ['بهمن ۱۴۰۱', '2023-01-21', '2023-02-20'],
        ['اسفند ۱۴۰۱', '2023-02-20', '2023-03-21'],
        ['فروردین ۱۴۰۲', '2023-03-21', '2023-04-21'],
        ['اردیبهشت ۱۴۰۲', '2023-04-21', '2023-05-22'],
        ['خرداد ۱۴۰۲', '2023-05-22', '2023-06-22'],
    ];


    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function get(): void
    {
        $disk = config('disks.MINIO_UPLOAD_EXCEL');
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = 'alaa_monthly_report_'.$now.'.xlsx';
        Excel::store(new DefaultClassExport($this->excelData(), array_values($this->excelHeaders())),
            'excel/'.$fileName, $disk);
    }

    private function excelData(): Collection
    {
        $excelCollection = collect();
        $rows = self::CALENDER;
        $columns = $this->excelHeaders();

        foreach ($rows as $key => $row) {
            $since = $row[1];
            $until = $row[2];
            $rowCollection = collect();
            foreach ($columns as $columnName => $column) {
                if ($columnName == 'month') {
                    continue;
                }
                //Uncomment this line for exporting a sample excel with zero values
                //$columnName = 'test';
                $rowCollection->push($this->makeColumnData($columnName, $since, $until));
            }

            $rowCollection->prepend($row[0]);
            $excelCollection->put($key, $rowCollection);
        }

        return $excelCollection;
    }

    private function excelHeaders(): array
    {
        return [
            'month' => 'ماه',
            'users_count' => 'تعداد کاربران',
            'customers_count' => 'تعداد خریداران',
            'orders_count' => 'تعداد سفارش ها',
            'orderproducts_count' => 'تعداد اقلام',
            'GMV' => 'GMV(بدون تخفیف به تومان)',
            'income' => 'درآمد (تومان)',
            'free_content_sum' => 'جمع فیلمهای رایگان تولید شده(دقیقه)',
            'paid_content_sum' => 'جمع فیلمهای پولی تولید شده(دقیقه)',
        ];
    }

    private function makeColumnData($columnName, $since, $until)
    {
        switch ($columnName) {
            case 'test':
                $columnData = 0;
                break;
            case 'users_count':
                $columnData =
                    User::where('created_at', '>=', $since.' 00:00:00')->where('created_at', '<',
                        $until.' 00:00:00')->whereDoesntHave('roles')->count();
                break;
            case 'customers_count':
                $columnData =
                    User::whereDoesntHave('roles')->whereHas('orders', function ($query) use ($since, $until) {
                        $query->where('completed_at', '>=', $since.' 00:00:00')->where('completed_at', '<',
                            $until.' 00:00:00')
                            ->where('orderstatus_id', Order::getDoneOrderStatus())
                            ->where('paymentstatus_id', [
                                config('constants.PAYMENT_STATUS_PAID'),
                                config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                            ])
//                            ->whereHas('successfulTransactions', function ($query) {
//                                $query->whereNull('wallet_id')
//                                    ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'));
//                            })
                        ;
                    })->count();
                break;
            case 'orderproducts_count':
                $columnData = Orderproduct::
//                where('orderproducttype_id' , config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                whereHas('order', function ($q) use ($since, $until) {
                    $q->where('completed_at', '>=', $since.' 00:00:00')->where('completed_at', '<', $until.' 00:00:00')
                        ->where('orderstatus_id', Order::getDoneOrderStatus())
                        ->where('paymentstatus_id', [
                            config('constants.PAYMENT_STATUS_PAID'),
                            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                        ])
//                                    ->whereHas('successfulTransactions', function ($query) {
//                                        $query->whereNull('wallet_id')
//                                            ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'));
//                                    })
                    ;
                })->count();
                break;
            case 'orders_count':
                $columnData = Order::
                where('completed_at', '>=', $since.' 00:00:00')->where('completed_at', '<', $until.' 00:00:00')
                    ->where('orderstatus_id', Order::getDoneOrderStatus())
                    ->where('paymentstatus_id',
                        [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
//                    ->whereHas('successfulTransactions', function ($query) {
//                            $query->whereNull('wallet_id')
//                            ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'));
//                    })
                    ->count();
                break;
            case 'GMV':
                $columnData = Orderproduct::
//                where('orderproducttype_id' , config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                whereHas('order', function ($q) use ($since, $until) {
                    $q->where('completed_at', '>=', $since.' 00:00:00')->where('completed_at', '<', $until.' 00:00:00')
                        ->where('orderstatus_id', Order::getDoneOrderStatus())
                        ->where('paymentstatus_id', [
                            config('constants.PAYMENT_STATUS_PAID'),
                            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                        ])
//                            ->whereHas('successfulTransactions', function ($query) {
//                                $query->whereNull('wallet_id')
//                                    ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'));
//                            })
                    ;
                })->sum('cost');
                break;
            case 'income':
                $columnData = Transaction::query()
                    ->whereHas('order', function ($q) use ($since, $until) {
                        $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                            ->whereIn('paymentstatus_id', [config('constants.PAYMENT_STATUS_PAID')]);
                    })
                    ->where('completed_at', '>=', $since.' 00:00:00')->where('completed_at', '<', $until.' 00:00:00')
                    ->whereNull('wallet_id')
                    ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'))
                    ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                    ->sum('cost');
                break;
            case 'free_content_sum':
                $durationInSec =
                    Content::where('created_at', '>=', $since.' 00:00:00')->where('created_at', '<',
                        $until.' 00:00:00')->where('contenttype_id', 8)->where('isFree', 1)->sum('duration');
                $columnData = (int) ($durationInSec / 60);
                break;
            case 'paid_content_sum':
                $durationInSec =
                    Content::where('created_at', '>=', $since.' 00:00:00')->where('created_at', '<',
                        $until.' 00:00:00')->where('contenttype_id', 8)->where('isFree', 0)->sum('duration');
                $columnData = (int) ($durationInSec / 60);
                break;
            default:
                $columnData = 'نامشخص';
                break;
        }

        return $columnData;
    }
}
