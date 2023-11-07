<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orderproduct;
use App\Models\Product;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.SHOW_ABRISHAM_ANALYTICS'), ['only' => ['abrisham',],]);
    }

    public function abrisham(Request $request)
    {
        $abrishamPaidOrderproducts =
            Orderproduct::whereIn('product_id',
                array_merge(array_keys(Product::ALL_ABRISHAM_PRODUCTS), array_keys(Product::ALL_ABRISHAM_PRO_PRODUCTS)))
                ->whereHas('order', function ($q) {
                    $q->paidAndClosed()->completedAfter('2022-07-09 21:45:00')->whereDoesntHave('transactions',
                        function ($q) {
                            $q->where('transactionstatus_id',
                                config('constants.TRANSACTION_STATUS_PENDING'))->where('paymentmethod_id',
                                config('constants.PAYMENT_METHOD_ATM'));
                        });
                })->get();

        $abrishamInInstalmentOrderproducts =
            Orderproduct::whereIn('product_id', [
                Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
                Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
            ])
                ->whereHas('order', function ($q) {
                    $q->inDebt()->completedAfter('2022-07-09 21:45:00')->whereDoesntHave('transactions', function ($q) {
                        $q->where('transactionstatus_id',
                            config('constants.TRANSACTION_STATUS_PENDING'))->where('paymentmethod_id',
                            config('constants.PAYMENT_METHOD_ATM'));
                    });
                })->get();

        $abrishamPackCount =
            $abrishamPaidOrderproducts->whereIn('product_id', Product::ALL_PACK_ABRISHAM_PRODUCTS)->count();
        $abrishamSingleCount =
            $abrishamPaidOrderproducts->whereIn('product_id', Product::ALL_SINGLE_ABRISHAM_PRODUCTS)->count();
        $abrishamProSingleCount = $abrishamPaidOrderproducts->whereIn('product_id', [
            Product::RAHE_ABRISHAM1401_PRO_SHIMI,
            Product::RAHE_ABRISHAM1401_PRO_ZIST,
            Product::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN,
            Product::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI,
            Product::RAHE_ABRISHAM1401_PRO_ADABIYAT,
            Product::RAHE_ABRISHAM1401_PRO_ARABI,
            Product::RAHE_ABRISHAM1401_PRO_DINI,
            Product::RAHE_ABRISHAM1401_PRO_ZABAN,
        ])->count();

        $abrishamProPackPaidCount = $abrishamPaidOrderproducts->whereIn('product_id', [
            Product::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI,
            Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
            Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
        ])->count();

        $abrishamProInInstalmentCount = $abrishamInInstalmentOrderproducts->whereIn('product_id', [
            Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
            Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
        ])->count();

        echo '<div dir="rtl" >';
        echo '<p style="font-weight:bold">'.'از ساعت 21:45 تاریخ  18 تیر 1401:'.'</p>';
        echo 'تعداد خرید تکی ابریشم معمولی: '.'<div style="color:darkred">'.number_format($abrishamSingleCount).'</div><br>';
        echo 'تعداد خرید پک ابریشم معمولی: '.'<div style="color:darkred">'.number_format($abrishamPackCount).'</div><br>';
        echo 'تعداد خرید تکی ابریشم پرو: '.'<div style="color:darkred">'.number_format($abrishamProSingleCount).'</div><br>';
        echo 'تعداد خرید پک ابریشم پرو نقدی: '.'<div style="color:darkred">'.number_format($abrishamProPackPaidCount).'</div><br>';
        echo 'تعداد خرید پک ابریشم پرو قسطی: '.'<div style="color:darkred">'.number_format($abrishamProInInstalmentCount).'</div><br>';
        echo '</div>';
    }
}