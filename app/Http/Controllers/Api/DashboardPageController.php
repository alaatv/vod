<?php

namespace App\Http\Controllers\Api;

use App\Classes\SEO\SeoDummyTags;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlockInWebAsset;
use App\Models\Product;
use App\Models\User;
use App\Models\Websitesetting;
use App\Traits\MetaCommon;
use App\Traits\User\AssetTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DashboardPageController extends Controller
{
    use MetaCommon;
    use AssetTrait;

    private $setting;

    public function __construct(Websitesetting $setting)
    {
        $this->setting = $setting->setting;
    }

    public function __invoke(Request $request, User $user)
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }

        if ($request->user()->id != $user->id) {
            abort(ResponseAlias::HTTP_FORBIDDEN, 'you can\'nt get user '.$user->id.' dashboard!.');
        }

        $userInfoCompletion = $user->completion();
        $hasAbrisham = $user->userHasAnyOfTheseProducts(array_keys(Product::ALL_ABRISHAM_PRODUCTS));

        $url = $request->url();
        //child has worker task
        $userAssetsCollection = BlockInWebAsset::collection($user->getDashboardBlocks());

        if ($request->expectsJson()) {
            return response()->json([
                'user_id' => $user->id,
                'data' => $userAssetsCollection,
            ]);
        }


        $this->generateSeoMetaTags(new SeoDummyTags('محصولات من',
            'داشبودر کاربری شما', $url,
            $url, route('image', [
                'category' => '11',
                'w' => '100',
                'h' => '100',
                'filename' => $this->setting->site->siteLogo,
            ]), '100', '100', null));

        $categoryArray = [
            [
                'name' => 'همه',
                'value' => 'all',
                'selected' => true,
            ],
            [
                'name' => 'راه ابریشم',
                'value' => 'VIP',
            ],
            [
                'name' => 'آرش',
                'value' => 'همایش/آرش',
            ],
            [
                'name' => 'تایتان',
                'value' => 'همایش/تایتان',
            ],
            [
                'name' => 'تفتان',
                'value' => 'همایش/تفتان',
            ],
            [
                'name' => 'گدار',
                'value' => 'همایش/گدار',
            ],
            [
                'name' => 'نظام قدیم',
                'value' => 'قدیم',
            ],
            [
                'name' => 'جزوه',
                'value' => 'جزوه',
            ],
            [
                'name' => 'آزمون',
                'value' => 'آزمون/سه آ',
            ],
        ];

        $isPanelLock = false;

        $purchasedOrderproducts = collect([
            [
                'id' => 1,
                'title' => 'راه ابریشم فیزیک',
                'photo' => 'https://nodes.alaatv.com/upload/images/product/04-100_20210623122601.jpg?w=331&h=331',
                'is_extendable' => false,
            ],
            [
                'id' => 2,
                'title' => 'راه ابریشم شیمی',
                'photo' => 'https://nodes.alaatv.com/upload/images/product/05-100_20210623122552.jpg?w=331&h=331',
                'is_extendable' => true,
            ],
            [
                'id' => 3,
                'title' => 'گام به گام فیزیک تجربی',
                'photo' => 'https://nodes.alaatv.com/upload/images/product/product_gbg_fizikt_20210515122122.jpg?w=331&h=331',
                'is_extendable' => true,
            ],
            [
                'id' => 4,
                'title' => 'جزوه ادبیات',
                'photo' => 'https://nodes.alaatv.com/upload/images/product/j98_99_20200915075432.jpg?w=331&h=331',
                'is_extendable' => true,
            ],
            [
                'id' => 5,
                'title' => 'تتا زیست شناسی',
                'photo' => 'https://nodes.alaatv.com/upload/images/product/Aks%20mahsool%20teta%20zist_20220220085837.jpg?w=331&h=331',
                'is_extendable' => false,
            ],
        ]);

        $extensionRequiredSurvey = collect([
            [
                'statement' => 'کنکور چه سالی شرکت می کنید؟',
                'input_name' => 'konkurYear',
                'choices' => [
                    [
                        'caption' => '1402',
                        'value' => 13,
                    ],
                    [
                        'caption' => '1403',
                        'value' => 14,
                    ],
                ],
            ],
            [
                'statement' => 'دانش آموز یا فارغ التحصیل هستید؟',
                'input_name' => 'studentOrGraduate',
                'choices' => [
                    [
                        'caption' => 'دانش آموز',
                        'value' => 1,
                    ],
                    [
                        'caption' => 'فارغ التحصیل',
                        'value' => 2,
                    ],
                ],
            ],
        ]);

        $infoText = collect([
//            [
//                'text' => 'درخواست شما برای تمدید محصولات طبق تیکت در حال بررسی است',
//                'link' => 'https://alaatv.com/t#/t/118394',
//            ],
        ]);

        return response()->json([
            'user' => $user,
            'userAssetsCollection' => $userAssetsCollection,
            'categoryArray' => $categoryArray,
            'userInfoCompletion' => $userInfoCompletion,
            'hasAbrisham' => $hasAbrisham,
            'isPanelLock' => $isPanelLock,
            'purchasedOrderproducts' => $purchasedOrderproducts,
            'extensionRequiredSurvey' => $extensionRequiredSurvey,
            'infoText' => $infoText
        ], 200);
    }
}