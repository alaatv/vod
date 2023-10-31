<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaftanSetResource;
use App\Models\Contentset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaftanDashboardPageController extends Controller
{
    public const MAJORS = [
        [
            'id' => 1,
            'title' => 'عمومی',
        ],
        [
            'id' => 2,
            'title' => 'اختصاصی ریاضی',
        ],
        [
            'id' => 3,
            'title' => 'اختصاصی تجربی',
        ]
//        [
//            "id" => 4,
//            "title" => 'انسانی',
//        ],
    ];
    public const MAP = [
        1430 => ['major' => 1, 'product' => 648],
        1431 => ['major' => 1, 'product' => 649],
        1432 => ['major' => 1, 'product' => 650],
        1433 => ['major' => 1, 'product' => 651],
        1434 => ['major' => 2, 'product' => 652],
        1435 => ['major' => 2, 'product' => 653],
        1436 => ['major' => 2, 'product' => 654],
        1437 => ['major' => 2, 'product' => 655],
        1438 => ['major' => 3, 'product' => 659],
        1439 => ['major' => 3, 'product' => 657],
        1440 => ['major' => 3, 'product' => 658],
        1441 => ['major' => 1, 'product' => 656],
//        1442 => ['major'=>4, 'product' => 660],
//        1443 => ['major'=>4, 'product' => 661],
    ];

    public function __invoke(Request $request)
    {
        $sets = Cache::tags(['taftan1400_dashboard', 'set'])
            ->remember('dashboard:taftan1400:sets', config('constants.CACHE_600'), function () {
                return Contentset::query()->whereIn('id', array_keys(static::MAP))->get();
            });
        $user = $request->user();
        $sets = TaftanSetResource::collection($sets);

        $banners = [
//            [
//                'url' => 'https://alaatv.com/',
//                'src' => 'https://nodes.alaatv.com/upload/images/slideShow/ad_abrisham_1401_20210726144636.jpg?w=541&h=253'
//            ],
//            [
//                'url' => false,
//                'src' => 'https://nodes.alaatv.com/upload/images/slideShow/ad_abrisham_1401_20210726144636.jpg?w=541&h=253'
//            ],
//            [
//                'src' => 'https://nodes.alaatv.com/upload/images/slideShow/ad_abrisham_1401_20210726144636.jpg?w=541&h=253'
//            ]
        ];

        $majors = static::MAJORS;
        return response()->json([

            'sets' => $sets,
            'user' => $user,
            'banners' => $banners,
            'majors' => $majors
        ], 200);
    }

}