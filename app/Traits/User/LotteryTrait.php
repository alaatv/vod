<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 16:08
 */

namespace App\Traits\User;



use Carbon\Carbon;

trait LotteryTrait
{
    public function getLottery()
    {
        $exchangeAmount = 0;
        $userPoints = 0;
        $userLottery = null;
        $prizeCollection = collect();
        $lotteryRank = null;
        $lottery = null;
        $lotteryMessage = '';
        $lotteryName = '';

        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
        $startTime2 = Carbon::create(2018, 06, 15, 07, 00, 00, 'Asia/Tehran');
        $endTime2 = Carbon::create(2018, 06, 15, 23, 59, 30, 'Asia/Tehran');
        $flag2 = ($now->between($startTime2, $endTime2));
        if (!$flag2) {

            return [
                $exchangeAmount,
                $userPoints,
                $userLottery,
                $prizeCollection,
                $lotteryRank,
                $lottery,
                $lotteryMessage,
                $lotteryName,
            ];
        }
        $bon = Bon::where('name', config('constants.BON2'))
            ->first();
        $userPoints = 0;
        if (isset($bon)) {
            $userPoints = $this->userHasBon($bon->name);
            $exchangeAmount = $userPoints * config('constants.HAMAYESH_LOTTERY_EXCHANGE_AMOUNT');
        }
        if ($userPoints <= 0) {
            $lottery = Lottery::where('name', config('constants.LOTTERY_NAME'))
                ->get()
                ->first();
            if (isset($lottery)) {
                $userLottery = $this->lotteries()
                    ->where('lottery_id', $lottery->id)
                    ->get()
                    ->first();
                if (isset($userLottery)) {
                    $lotteryName = $lottery->displayName;
                    $lotteryMessage = 'شما در قرعه کشی '.$lotteryName.' شرکت داده شدید و متاسفانه برنده نشدید.';
                    if (isset($userLottery->pivot->prizes)) {
                        $lotteryRank = $userLottery->pivot->rank;
                        if ($lotteryRank == 0) {
                            $lotteryMessage = 'شما از قرعه کشی '.$lotteryName.' انصراف دادید.';
                        } else {
                            $lotteryMessage =
                                'شما در قرعه کشی '.$lotteryName.' برنده '.$lotteryRank.' شدید.';
                        }

                        $prizes = json_decode($userLottery->pivot->prizes)->items;
                        $prizeCollection = collect();
                        foreach ($prizes as $prize) {
                            if (isset($prize->objectId)) {
                                $id = $prize->objectId;
                                $model_name = $prize->objectType;
                                $model = new $model_name();
                                $model->find($id);

                                $prizeCollection->push(['name' => $prize->name]);
                            } else {
                                $prizeCollection->push(['name' => $prize->name]);
                            }
                        }
                    }
                }
            }
        }


        return [
            $exchangeAmount,
            $userPoints,
            $userLottery,
            $prizeCollection,
            $lotteryRank,
            $lottery,
            $lotteryMessage,
            $lotteryName,
        ];
    }

    public function lotteries()
    {
        return $this->belongsToMany(Lottery::class)
            ->withPivot('rank', 'prizes');
    }
}
