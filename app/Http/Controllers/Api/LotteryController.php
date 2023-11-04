<?php

namespace App\Http\Controllers\Web;

use App\Bon;
use App\Classes\Lottery\LotteryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\LotteryCreateRequest;
use App\Http\Requests\LotteryUpdateRequest;
use App\Lottery;
use App\LotteryStatus;
use App\Traits\UserCommon;
use App\User;
use App\Userbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LuckyBox\Card\IdCard;
use LuckyBox\LuckyBox;

class LotteryController extends Controller
{
    use UserCommon;

    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $lotteries = Lottery::with(['users'])->latest()->get();
        return view('lottery.index', compact('lotteries'));
    }

    public function show(Request $request, Lottery $lottery)
    {
        $users = $lottery->users;
        $status = $lottery->status;
        return view('lottery.show', compact('lottery', 'users', 'status'));
    }

    public function store(LotteryCreateRequest $request)
    {
        $prizes = $request->get('prizes');
        $jsonPrizes = [];
        foreach ($prizes as $prize) {
            $tmp = explode(':', $prize);
            $jsonPrizes[$tmp[0]] = $tmp[1];
        }
        $lottery = Lottery::create([
            'name' => $request->get('name'),
            'displayName' => $request->get('displayName'),
            'holdingDate' => $request->get('holdingDate'),
            'prizes' => json_encode($jsonPrizes),
        ]);
        return redirect()->route('lottery.index')->with('message', "$lottery->displayName  با موفقیت ساخته شد ");
    }

    public function create(Request $request)
    {
        return view('lottery.create');
    }

    public function edit(Request $request, Lottery $lottery)
    {
        $prizes = json_decode($lottery->prizes);
        return view('lottery.edit', compact('lottery', 'prizes'));
    }

    /**
     * Holding the lottery
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function holdLottery(Request $request)
    {
        $counter = 0;
        $successCounter = 0;
        $failedCounter = 0;
        $warningCounter = 0;
        try {
            // Setup
            $lotteryName = '';
            if ($request->has('lottery')) {
                $lotteryName = $request->get('lottery');
            }

            $lottery = Lottery::where('name', $lotteryName)->first();
            if (!isset($lottery)) {
                dd('Lottery not found!');
            }

            $bonName = config('constants.BON2');
            $bon = Bon::where('name', $bonName)
                ->first();
            if (!isset($bon)) {
                dd('Unexpected error! bon not found.');
            }

            $luckyBox = new LuckyBox();
            $luckyBox->setConsumable(true);

            $participants = Userbon::where('bon_id', $bon->id)
                ->where('userbonstatus_id', 1)
                ->get();

            dump('Number of userbons: '.$participants->count());
            dump('Sum of total points: '.$participants->sum('totalNumber'));

            foreach ($participants as $participant) {
                $points = $participant->totalNumber - $participant->usedNumber;
                for ($i = $points; $i > 0; $i--) {
                    $card = new IdCard();
                    $card->setId($participant->user->id)
                        ->setRate(100);
                    $luckyBox->add($card);
                }
            }
            dump($luckyBox);
//            dd('stop');
            // Draw
            $winners = [];
            while (!$luckyBox->isEmpty()) {
                $card = $luckyBox->draw();
                $cardId = $card->getId();

                $user = User::where('id', $cardId)->first();
                if (isset($user)) {
                    $userbon = $user->userbons->where('bon_id', $bon->id)
                        ->where('userbonstatus_id', 1)
                        ->first();

                    if (isset($userbon)) {
                        $userbon->userbonstatus_id = 3;
                        $userbon->usedNumber = $userbon->totalNumber;
                        $userbon->update();
                    }

                    if (in_array($cardId, $winners)) {
                        continue;
                    }

                    $userlotteries = $user->lotteries->where('lottery_id', $lottery->id);
                    if ($userlotteries->isEmpty()) {
                        $counter++;
                        $user->lotteries()
                            ->attach($lottery->id, ['rank' => $counter]);
                        echo "<span style='color:red;font-weight: bolder'>"."#$counter: ".'</span>'.$user->full_name." - $user->mobile"." - $user->nationalCode".' Points: '.$userbon->totalNumber;
                        echo '<br>';
                        $successCounter++;

                        //                        [
                        //                            $prizeName ,
                        //                            $amount
                        //                        ]= $lottery->prizes($counter);

                        //                      $user->notify(new LotteryWinner($lottery , $counter , $prizeName));
                        //                      echo '<span style='color:green;font-weight: bolder'>User notified</span>';
                        //                      echo '<br>';

                        array_push($winners, $cardId);
                    } else {
                        if ($userlotteries->first()->pivot->rank == 0) {
                            dump('Warning! User '.$user->id.' had been removed from lottery');
                            $warningCounter++;
                        } else {
                            dump('Error : User '.$user->id.' had been participated in lottery with rank > 0');
                            $failedCounter++;
                        }
                    }
                } else {
                    dump('Warning! #$counter was not found! User id: '.$card->getId());
                    $warningCounter++;
                }
            }

            dump('number of successfully processed users: '.$successCounter);
            dump('number of failed users: '.$failedCounter);
            dump('number of warnings: '.$warningCounter);
            dd('finish');
        } catch (Exception $e) {
            dump($successCounter.' users successfully done');
            dump($failedCounter.' users failed');
            dump($warningCounter.' warnings');

            return response()->json([
                'message' => 'unexpected error',
                'number of successfully processed items' => $counter,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    public function update(LotteryUpdateRequest $request, Lottery $lottery)
    {
        $prizes = $request->get('prizes');
        $jsonPrizes = [];
        foreach ($prizes as $index => $prize) {
            $tmp = explode(':', $prize);
            $jsonPrizes[$tmp[0]] = $tmp[1];
        }
        $lottery->update([
            'name' => $request->get('name'),
            'displayName' => $request->get('displayName'),
            'holdingDate' => $request->get('holdingDate'),
            'prizes' => json_encode($jsonPrizes),
        ]);
        return redirect()->route('lottery.index')->with('message', "$lottery->displayName  با موفقیت بروزرسانی شد ");
    }

    /**
     * Giving prizes the lottery winners
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function givePrizes(Request $request)
    {
        try {
            $lotteryName = '';
            if ($request->has('lottery')) {
                $lotteryName = $request->get('lottery');
            }

            $lottery = Lottery::where('name', $lotteryName)
                ->first();
            if (!isset($lottery)) {
                dd('Lottery not found!');
            }

            /** @var Lottery $lottery */
            $userlotteries = $lottery->users->sortBy('pivot.rank');

            $successCounter = 0;
            $failedCounter = 0;
            dump('Number of participants: '.$userlotteries->count());
            foreach ($userlotteries as $userlottery) {
                $rank = $userlottery->pivot->rank;
                [
                    $prizeName,
                    $amount,
                    $memorial,
                ] = $lottery->prizes($rank);

                $done = true;
                $prizeInfo = '';
                if ($amount > 0) {
                    $depositResult = $userlottery->deposit($amount, config('constants.WALLET_TYPE_GIFT'));
                    $done = $depositResult['result'];
                    $responseText = $depositResult['responseText'];

                    if ($done) {
//                        $userlottery->notify(new GiftGiven($amount));
                        echo "<span style='color:green' >".'Wallet notification sent to user :'.$userlottery->lastName.'</span>';
                        echo '<br>';

                        $objectId = $depositResult['wallet'];
                        $prizeInfo = '
                          "objectType": "App\\\\Wallet",
                          "objectId": "'.$objectId.'"
                          ';
                    }
                } else {
                    if (strlen($memorial) > 0) {
                        $objectId = 543;
                        $prizeInfo = '
                          "objectType": "App\\\\Coupon",
                          "objectId": "'.$objectId.'"
                          ';
                    }
                }

                if ($done) {
                    if (strlen($prizeName) == 0) {
//                        $userlottery->notify(new LotteryWinner($lottery, $rank, $prizeName, $memorial));
                        echo "<span style='color:green;font-weight: bolder'>User ".$userlottery->mobile.' with rank '.$rank.' notified</span>';
                        echo '<br>';
                    }

                    $itemName = '';
                    if (strlen($prizeName) > 0) {
                        $itemName = $prizeName;
                    } else {
                        if (strlen($memorial) > 0) {
                            $itemName = $memorial;
                        }
                    }

                    $prizes = '';
                    if (strlen($prizeInfo) > 0) {
                        $prizes = '{
                      "items": [
                        {
                          "name": "'.$itemName.'",'.$prizeInfo.'}
                      ]
                    }';
                    } else {
                        if (strlen($itemName) > 0) {
                            $prizes = '{
                      "items": [
                        {
                          "name": "'.$itemName.'"
                        }
                          ]
                    }';
                        }
                    }

                    $pivotArray = [];
                    if (strlen($prizes) > 0) {
                        $pivotArray['prizes'] = $prizes;
                    }

                    if (!empty($pivotArray)) {
                        $givePrizeResult = $userlottery->lotteries()
                            ->where('lottery_id', $lottery->id)
                            ->where('pivot.rank',
                                $rank)
                            ->updateExistingPivot($lottery->id, $pivotArray);

                        if (!$givePrizeResult) {
                            dump('Failed on updating prize for user: '.$userlottery->id);
                            $failedCounter++;
                        } else {
                            $successCounter++;
                        }
                    } else {
                        $successCounter++;
                    }
                } else {
                    dump('Failed on updating wallet for user '.$userlottery->id.' '.$responseText);
                    $failedCounter++;
                }
            }
            dump('Successfully processed users '.$successCounter);
            dump('ّFiled users: '.$failedCounter);
            dd('done');
        } catch (Exception $e) {
            return response()->json([
                'message' => 'unexpected error',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * Removes user from lottery
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function removeFromLottery(Request $request)
    {
        $user = $request->user();
        $message = '';

        $bonName = config('constants.BON2');
        $bon = Bon::where('name', $bonName)
            ->first();
        if (isset($bon)) {
            $userbons = $user->userValidBons($bon);
            [$usedUserBon, $userBonTaken, $done] = $this->checkUserBonsForLottery($user, $message, $userbons);
        } else {
            $done = false;
            $message = 'خطای غیر منتظره . لطفا بعدا اقدام فرمایید';
        }

        if ($done) {
            return $this->makeResponseForRemoveFromLotteryMethod(Response::HTTP_OK, 'OK');
        }

        if (!isset($userBonTaken) || !$userBonTaken) {
            return $this->makeResponseForRemoveFromLotteryMethod(Response::HTTP_SERVICE_UNAVAILABLE, $message);
        }

        foreach ($userbons as $userbon) {
            if (isset($usedUserBon[$userbon->id])) {
                $usedNumber = $usedUserBon[$userbon->id]['used'];
                $userbon->usedNumber = max($userbon->usedNumber - $usedNumber, 0);
                $userbon->userbonstatus_id = config('constants.USERBON_STATUS_ACTIVE');
            } else {
                $userbon->usedNumber = 0;
                $userbon->userbonstatus_id = config('constants.USERBON_STATUS_ACTIVE');
            }

            $userbon->update();
        }

        return $this->makeResponseForRemoveFromLotteryMethod(Response::HTTP_SERVICE_UNAVAILABLE, $message);
    }

    private function checkUserBonsForLottery($user, string &$message, $userbons): array
    {
        $done = false;
        if (!$userbons->isNotEmpty()) {
            $message = 'شما در قرعه کشی نیستید';
            return [null, null, $done];
        }

        [$usedUserBon, $sumBonNumber] = $this->getUsedUserBon($userbons);
        $userBonTaken = true;
        [
            $result,
            $responseText,
            $prizeName,
            $walletId,
        ] = $this->exchangeLottery($user, $sumBonNumber);

        if (!$result) {
            $message = $responseText;
            return [$usedUserBon, $userBonTaken, $done];
        }
        $lottery = Lottery::where('name', config('constants.LOTTERY_NAME'))
            ->first();

        if (!isset($lottery)) {
            $message = 'خطای غیر منتظره. لطفا بعدا دوباره اقدام نمایید';
            return [$usedUserBon, $userBonTaken, $done];
        }
        $prizes = '{
                      "items": [
                        {
                          "name": "'.$prizeName.'",
                          "objectType": "App\\\\Wallet",
                          "objectId": "'.$walletId.'"
                        }
                      ]
                    }';
        if ($user->lotteries()
            ->where('lottery_id', $lottery->id)
            ->get()
            ->isEmpty()) {
            $attachResult = $user->lotteries()
                ->attach($lottery->id, [
                    'rank' => 0,
                    'prizes' => $prizes,
                ]);
            $done = true;
        } else {
            $message = 'شما قبلا از قرعه کشی انصراف داده اید';
        }

        return [$usedUserBon, $userBonTaken, $done];
    }

    /**
     * @param $userbons
     *
     * @return array
     */
    private function getUsedUserBon($userbons): array
    {
        $usedUserBon = collect();
        $sumBonNumber = 0;
        /** @var Userbon $userbon */
        foreach ($userbons as $userbon) {
            $totalBonNumber = $userbon->totalNumber - $userbon->usedNumber;
            $usedUserBon->put($userbon->id, ['used' => $totalBonNumber]);
            $sumBonNumber += $totalBonNumber;
            $userbon->usedNumber = $userbon->usedNumber + $totalBonNumber;
            $userbon->userbonstatus_id = config('constants.USERBON_STATUS_USED');
            $userbon->update();
        }

        return [$usedUserBon, $sumBonNumber];
    }

    /**
     * @param  int  $httpStatus
     * @param  string  $message
     *
     * @return ResponseFactory|Response
     */
    private function makeResponseForRemoveFromLotteryMethod(int $httpStatus, string $message)
    {
        return response([
            ['message' => $message],
        ], $httpStatus);
    }

    public function destroy(Request $request, Lottery $lottery)
    {
        $lottery->delete();
        return redirect()->route('lottery.index')->with('message', "$lottery->displayName با موفقیت حذف شد ");
    }

    public function actionOnLottery(Request $request, Lottery $lottery, $action)
    {
        $handler = new LotteryHandler($lottery, $request);
        $result = $handler->{$action}();
        if ($action != LotteryStatus::HOLDED) {
            return redirect()->back()->with('message', $result);
        }
        return redirect()->back()->with('result', $result);

    }
}