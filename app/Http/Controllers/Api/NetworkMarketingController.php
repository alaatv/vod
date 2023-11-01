<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\ConsumerCodeRequest;
use App\Models\Bon;
use App\Models\User;
use App\Repositories\NetworkMarketingRepo;
use App\Repositories\ReferralCodeUserRepo;
use App\Repositories\UserbonRepo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NetworkMarketingController extends Controller
{
    protected mixed $bonName;

    public function __construct()
    {
        $this->bonName = config('constants.BON2');
        $this->middleware('userCanUserOnlyOneReferalCode', ['only' => ['useCode',],]);
        $this->middleware('throttle:2,1');
    }

    public function getPackScores(Request $request)
    {
        return response()->json(['message' => 'استفاده از انار طلایی غیر فعال شده است'], Response::HTTP_LOCKED);
        /** @var User $user */
        $user = $request->user();
        if (!isset($user)) {
            return myAbort(Response::HTTP_BAD_REQUEST, trans('yalda1400.login required'));
        }
        $inYaldaSingleAbrishamsCount = $user->countInYaldaSingleAbrishams();
        $packsCount = $user->countBeforeYaldaPackAbrishams();

        if (!($packsCount && !$user->isUserLotteryPointsBalance($packsCount, $inYaldaSingleAbrishamsCount))) {

            return myAbort(Response::HTTP_BAD_REQUEST, trans('yalda1400.user has not purchased any Abrisham packages'));

        }

        UserbonRepo::createActiveUserBon($user->id, $packsCount, Bon::ALAA_POINT_BON);

//            $user->notify(new Yalda14001());

        return response()->json([
            'data' =>
                [
                    'number_of_scores' => $packsCount,
                ]
        ]);
    }

    public function useCode(ConsumerCodeRequest $request)
    {
        $referralCode = NetworkMarketingRepo::getReferralCodeInstance($request->code,
            config('constants.EVENTS.YALDA_1400'))->first();

        $user = $request->user();
        $openOrder = $user->getOpenOrderOrCreate();
        ReferralCodeUserRepo::create($openOrder, $referralCode);

        $chances = $user->calcYaldaChances();
        // return response()->json(['message' => 'استفاده از کد معرف غیر فعال شده است'], ResponseAlias::HTTP_LOCKED);
        return response()->json(['data' => ['chances' => $chances]], ResponseAlias::HTTP_OK);
    }
}