<?php

namespace App\Traits;





use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

trait DonateCommonTrait
{
    private function donateData()
    {
        $yearSpend = config('constants.ALAA_YEARLY_SPEND');
        $monthToPeriodConvert = collect(config('constants.JALALI_CALENDER'));
        $firstMonth = $monthToPeriodConvert->first()['month'];

        $orders = $this->repo_getOrders();

        $currentGregorianDate = Carbon::now()->timezone('Asia/Tehran');
        $currentGregorianYear = $currentGregorianDate->year;
        [
            $currentJalaliYear, $currentJalaliMonth, $currentJalaliDay
        ] = $this->todayJalaliSplittedDate($currentGregorianDate);
        $currentJalaliMonthString = $this->convertToJalaliMonth($currentJalaliMonth);
        $currentJalaliMonthDays = $this->getJalaliMonthDays($currentJalaliMonthString);

        $currentJalaliDateString = $currentJalaliDay.' '.$currentJalaliMonthString;

        $latestDonors = $this->latestDonations($orders);
        $maxDonors = $this->maxDonationsLastMonth($monthToPeriodConvert, $currentJalaliMonthString, $orders);
        [$chartData, $totalIncome, $totalSpend, $months] = $this->chartData(
            $currentJalaliMonthString,
            $firstMonth,
            $currentJalaliDay,
            $monthToPeriodConvert,
            $orders,
            $currentGregorianYear,
            $currentJalaliMonthDays
        );

        return [
            $latestDonors,
            $maxDonors,
            $months,
            $chartData,
            $totalSpend,
            $totalIncome,
            $currentJalaliDateString,
            $currentJalaliMonthString,
            $yearSpend,
        ];
    }

    /**
     * @return Order[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Builder[]|Collection
     */
    private function repo_getOrders()
    {
        return Order::whereHas('orderproducts', function ($q) {
            $q->whereIn('product_id', Product::DONATE_PRODUCT_ARRAY);
        })
            ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'))
            ->orderBy('completed_at', 'DESC')
            ->get();
    }

    /**
     * @param $orders
     * @return Collection
     */
    private function latestDonations($orders): Collection
    {
        $latestDonors = collect();
        $donates = $orders->take(config('constants.DONATE_PAGE_LATEST_WEEK_NUMBER'));
        foreach ($donates as $donate) {
            if (isset($donate->user_id)) {
                /** @var User $user */
                $user = $donate->user;
                $id = $user->id;
                $firstName = $user->firstName;
                $lastName = $user->lastName;
                $avatar = $user->getCustomSizePhoto(150, 150, config('disks.PROFILE_IMAGE_MINIO'));
            }

            $donateAmount = $donate->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                ->whereIn('product_id', Product::DONATE_PRODUCT_ARRAY)
                ->get()
                ->sum('cost');

            $latestDonors->push([
                'id' => $id ?? '',
                'firstName' => $firstName ?? '',
                'lastName' => $lastName ?? '',
                'donateAmount' => $donateAmount,
                'avatar' => $avatar ?? '',
            ]);
        }

        return $latestDonors;
    }

    /**
     * @param $monthToPeriodConvert
     * @param $currentJalaliMonthString
     * @param $orders
     * @return Collection
     */
    private function maxDonationsLastMonth($monthToPeriodConvert, $currentJalaliMonthString, $orders): Collection
    {
        $today = $monthToPeriodConvert->where('month', $currentJalaliMonthString)->first();
        $today = $today['periodBegin'];
        $today = explode('-', $today);
        $todayYear = $today[0];
        $todayMonth = $today[1];
        $todayDay = $today[2];
        $date = Carbon::createMidnightDate($todayYear, $todayMonth, $todayDay);
        $thisMonthDonates = $this->repo_getThisMonthDonates($orders, $date);
        $maxDonates = $this->repo_MaxDonates($thisMonthDonates, config('constants.DONATE_PAGE_LATEST_MAX_NUMBER'));
        $maxDonors = collect();
        foreach ($maxDonates as $maxDonate) {
            $order = $maxDonate->order;
            if (isset($order->user_id)) {
                $user = $order->user;
                $id = $user->id;
                $firstName = $user->firstName;
                $lastName = $user->lastName;
                $avatar = $user->getCustomSizePhoto(150, 150, config('disks.PROFILE_IMAGE_MINIO'));
            }

            $donateAmount = $maxDonate->cost;

            $maxDonors->push([
                'id' => $id ?? '',
                'firstName' => $firstName ?? '',
                'lastName' => $lastName ?? '',
                'donateAmount' => $donateAmount,
                'avatar' => $avatar ?? '',
            ]);
        }

        return $maxDonors;
    }

    /**
     * @param $orders
     * @param $date
     *
     * @return mixed
     */
    private function repo_getThisMonthDonates($orders, $date)
    {
        $thisMonthDonates = $orders->where('completed_at', '>=', $date)
            ->pluck('id')
            ->toArray();

        return $thisMonthDonates;
    }

    /**
     * @param  array  $thisMonthDonates
     *
     * @return Collection
     */
    private function repo_MaxDonates(array $thisMonthDonates): Collection
    {
        $maxDonates = Orderproduct::whereIn('order_id', $thisMonthDonates)
            ->where(function ($q) {
                $q->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'));
            })
            ->whereIn('product_id', Product::DONATE_PRODUCT_ARRAY)
            ->orderBy('cost', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->take(config('constants.DONATE_PAGE_LATEST_MAX_NUMBER'))
            ->get();

        return $maxDonates;
    }

    /**
     * @param $currentJalaliMonthString
     * @param $firstMonth
     * @param $currentJalaliDay
     * @param $monthToPeriodConvert
     * @param $orders
     * @param $currentGregorianYear
     * @param $currentJalaliMonthDays
     * @return array
     */
    private function chartData(
        $currentJalaliMonthString,
        $firstMonth,
        $currentJalaliDay,
        $monthToPeriodConvert,
        $orders,
        $currentGregorianYear,
        $currentJalaliMonthDays
    ): array {
        $allMonths = config('constants.JALALI_ALL_MONTHS');
        $allDays = config('constants.ALL_DAYS_OF_MONTH');

        $chartData = collect();
        $totalSpend = 0;
        $totalIncome = 0;

        if ($currentJalaliMonthString == $firstMonth) {
            $months = [];
            $currentDayKey = array_search($currentJalaliDay, $allDays);
            $days = array_splice($allDays, 0, $currentDayKey + 1);
            $date = $monthToPeriodConvert->where('month', $currentJalaliMonthString)->first();
            foreach ($days as $day) {
                $mehrGregorianMonth = Carbon::createFromFormat('Y-m-d', $date['periodBegin'])
                    ->setTimezone('Asia/Tehran')->month;

                $mehrGregorianEndDay = Carbon::createFromFormat('Y-m-d', $date['periodEnd'])
                        ->setTimezone('Asia/Tehran')->day + ($day - 1);

                if ($mehrGregorianEndDay > 30) {
                    $mehrGregorianMonth++;
                    $mehrGregorianEndDay = $mehrGregorianEndDay - 30;
                    if ($mehrGregorianEndDay < 10) {
                        $mehrGregorianEndDay = '0'.$mehrGregorianEndDay;
                    }
                }
                if ($mehrGregorianMonth < 10) {
                    $mehrGregorianMonth = '0'.$mehrGregorianMonth;
                }

                $donates = $orders->where('completed_at', '>=',
                    "$currentGregorianYear-$mehrGregorianMonth-$mehrGregorianEndDay 00:00:00")
                    ->where('completed_at', '<=',
                        "$currentGregorianYear-$mehrGregorianMonth-$mehrGregorianEndDay 23:59:59");

                $totalMonthIncome = 0;
                foreach ($donates as $donate) {

                    $amount = $this->repo_getTotal($donate);

                    $totalMonthIncome += $amount;
                }
                $dayRatio = 1 / $currentJalaliMonthDays;
                $totalMonthSpend = (int) round(config('constants.AlAA_MONTHLY_SPEND') * $dayRatio);

                $totalIncome += $totalMonthIncome;
                $totalSpend += $totalMonthSpend;

                $monthData = $day.' '.$currentJalaliMonthString;
                $chartData->push([
                    'month' => $monthData,
                    'totalIncome' => $totalMonthIncome,
                    'totalSpend' => $totalMonthSpend,
                ]);
            }
        } else {
            $currentMonthKey = array_search($currentJalaliMonthString, $allMonths);
            $months = array_splice($allMonths, 0, $currentMonthKey + 1);

            foreach ($months as $month) {
                $date = $monthToPeriodConvert->where('month', $month)->first();
                $donates = $orders->where('completed_at', '>=', $date['periodBegin'])->where('completed_at', '<=',
                    $date['periodEnd']);

                $totalMonthIncome = 0;
                foreach ($donates as $donate) {
                    $amount = $this->repo_getTotal($donate);

                    $totalMonthIncome += $amount;
                }

                $totalMonthSpend = config('constants.AlAA_MONTHLY_SPEND');
                if ($month == $currentJalaliMonthString) {
                    $dayRatio = $currentJalaliDay / $currentJalaliMonthDays;
                    $totalMonthSpend = (int) round(config('constants.AlAA_MONTHLY_SPEND') * $dayRatio);
                }

                $totalIncome += $totalMonthIncome;
                $totalSpend += $totalMonthSpend;
                if ($month == $currentJalaliMonthString) {
                    $monthData = $currentJalaliDay.' '.$month;
                } else {
                    $monthData = $month;
                }

                $chartData->push([
                    'month' => $monthData,
                    'totalIncome' => $totalMonthIncome,
                    'totalSpend' => $totalMonthSpend,
                ]);
            }
        }

        return [$chartData, $totalIncome, $totalSpend, $months];
    }

    /**
     * @param $donate
     * @return mixed
     */
    private function repo_getTotal($donate)
    {
        $amount = $donate->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->whereIn('product_id', Product::DONATE_PRODUCT_ARRAY)
            ->get()
            ->sum('cost');

        return $amount;
    }
}
