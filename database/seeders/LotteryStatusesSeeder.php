<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use src\Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class LotteryStatusesSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['id' => '1', 'display_title' => 'غیر فعال', 'title' => 'inactive',],
            ['id' => '2', 'display_title' => 'آماده امتیاز دهی', 'title' => 'scoring',],
            ['id' => '3', 'display_title' => 'درحال امتیاز دهی', 'title' => 'waitForScoring',],
            ['id' => '4', 'display_title' => 'خطا در امتیاز دهی', 'title' => 'reportScoringError',],
//            ['id' => '5', 'display_title' => 'در حال بررسی خطای امتیازدهی', 'title' => 'wait',],
            ['id' => '5', 'display_title' => 'آماده برگزاری', 'title' => 'holdLottery',],
            ['id' => '6', 'display_title' => 'درحال برگزاری', 'title' => 'waitForHoldingLottery',],
            ['id' => '7', 'display_title' => 'خطا در برگزاری', 'title' => 'reportHoldingError',],
//            ['id' => '9', 'display_title' => 'در حال بررسی خطای برگزاری', 'title' => 'wait',],
            ['id' => '8', 'display_title' => 'برگزارشده', 'title' => 'holded'],
        ];
        DB::table('lottery_status')
            ->insert($statuses);
    }
}
