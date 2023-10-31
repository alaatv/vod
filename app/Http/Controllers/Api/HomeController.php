<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\Models\Content;
use App\Models\Product;
use App\Models\User;
use App\Repositories\SubscriptionRepo;
use App\Traits\CharacterCommon;
use App\Traits\DateTrait;
use App\Traits\Helper;
use App\Traits\User\AssetTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    use AssetTrait;
    use Helper;
    use CharacterCommon;
    use DateTrait;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['satra', 'debug']]);
    }

    public function debug(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'debug' => 2,
        ]);
    }

    public function authTestV2(Request $request)
    {
        return (new UserResource($request->user()))->response();
    }

    public function satra()
    {
        $contents = Cache::tags(['satra'])->remember('satra_api', config('constants.CACHE_60'), function () {
            return Content::query()
                ->orderByDesc('created_at')
                ->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))
                ->active()
                ->limit(5)
                ->get();
        });

        $contentArray = [];
        foreach ($contents as $content) {
            $validSince = $content->ValidSince_Jalali(false);
            $createdAt = $content->CreatedAt_Jalali();
            $contentArray[] = [
                'id' => $content->id,
                'url' => $content->url,
                'title' => $content->name,
                'published_at' => isset($validSince) ? $validSince : $createdAt,
                'visit_count' => 0,
            ];
        }

        return response()->json($contentArray);
    }

    public function getUserTelescopeExpiration(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        foreach (Product::TIMEPOINT_SUBSCRIPTON_PRODUCTS as $subscriptionProduct) {
            $subscription = SubscriptionRepo::validProductSubscriptionOfUser($user->id, [$subscriptionProduct]);
            if (isset($subscription)) {
                $expirationDateTime = $subscription->valid_until;
                break;
            }
        }

        return response()->json(
            [
                'data' => [
                    'expire_at' => $expirationDateTime ?? null,
                ]
            ]
        );
    }

    public function getKonkur1403Countdown()
    {
        $firstTurn = Carbon::parse('2024-04-25');
        $secondTurn = Carbon::parse('2024-06-27');
        $now = Carbon::now();
        return response()->json([
            'data' => [
                'now' => $now,
                'tillFirstTurn' => $firstTurn->diffInDays($now),
                'tillSecondTurn' => $secondTurn->diffInDays($now),
            ]
        ]);

    }

    public function phpPing(Request $request)
    {
        echo 'PONG!';
    }
}
