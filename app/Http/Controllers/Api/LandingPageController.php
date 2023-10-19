<?php

namespace App\Http\Controllers\Api;

use App\Collection\TransactionCollection;
use App\Http\Controllers\Controller;
use App\Models\Gender;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LandingPageController extends Controller
{
    public const ROOZE_DANESH_AMOOZ_USER_NECESSARY_INFO = [
        'firstName',
        'lastName',
        'major_id',
        'gender_id',
        'city',
        'province',
        'mobile_verified_at',
        'birthdate',
    ];

    public const ROOZE_DANESH_AMOOZ_GIFT_CREDIT = 14000;

    public function __construct()
    {
        $this->callMiddlewares($this->getAuthArray());
    }

    /**
     * @param  array  $auth
     */
    private function callMiddlewares(array $auth): void
    {
        $this->middleware('auth', ['only' => $auth]);
    }

    /**
     * @return array
     */
    private function getAuthArray(): array
    {
        return ['roozeDaneshAmooz'];
    }

    public function roozeDaneshAmooz(Request $request)
    {
        $hasGotGiftBefore = false;
        $hadGotGiftBefore = false;
        $user = $request->user();
        $genders = Gender::pluck('name', 'id')->prepend('نامشخص');
        $majors = Major::pluck('name', 'id')->prepend('نامشخص');

        $userCompletion = $user->completion('custom', self::ROOZE_DANESH_AMOOZ_USER_NECESSARY_INFO);
        if ($userCompletion != 100) {

            return view('user.completeRegister2',
                compact('user', 'hasGotGiftBefore', 'hadGotGiftBefore', 'genders', 'majors'));
        }
        $wallet = $user->wallets->where('wallettype_id', config('constants.WALLET_TYPE_GIFT'))->first();
        if (isset($wallet)) {
            /** @var TransactionCollection $depositTransactions */
            $depositTransactions =
                $wallet->transactions->where('cost', '<', 0)->where('created_at', '>=', '2019-11-03 00:00:00');
            if ($depositTransactions->isNotEmpty()) {
                $hadGotGiftBefore = true;
            }
        }

        if (!$hadGotGiftBefore) {
            $depositResult =
                $user->deposit(self::ROOZE_DANESH_AMOOZ_GIFT_CREDIT, config('constants.WALLET_TYPE_GIFT'));
            if ($depositResult['result']) {
                $hasGotGiftBefore = true;
            } else {
                $hasGotGiftBefore = false;
            }
        }


        return view('user.completeRegister2',
            compact('user', 'hasGotGiftBefore', 'hadGotGiftBefore', 'genders', 'majors'));
    }

    public function roozeDaneshAmooz2()
    {
        return redirect(route('web.landing.13Aban'), Response::HTTP_MOVED_PERMANENTLY);
    }
}
