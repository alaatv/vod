<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonateResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Websitesetting;
use App\Traits\DateTrait;
use App\Traits\DonateCommonTrait;
use App\Traits\MetaCommon;
use Illuminate\Http\Request;

class DonateController extends Controller
{
    use MetaCommon;
    use DateTrait;
    use DonateCommonTrait;

    private $setting;

    public function __construct(Websitesetting $setting)
    {
        $this->setting = $setting->setting;
    }

    public function __invoke(Request $request)
    {
        // Notice: Please don't remove unused params.
        [
            $latestDonors,
            $maxDonors,
            $months,
            $chartData,
            $totalSpend,
            $totalIncome,
            $currentJalaliDateString,
            $currentJalaliMonthString,
            $yearSpend,
        ] = $this->donateData();

        $fakeResource = Order::whereHas('orderproducts', function ($q) {
            $q->whereIn('product_id', Product::DONATE_PRODUCT_ARRAY);
        })
            ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'))
            ->first();

        return new DonateResource(
            $fakeResource,
            $latestDonors,
            $maxDonors,
            $chartData,
            $totalIncome,
            $totalSpend
        );
    }
}
