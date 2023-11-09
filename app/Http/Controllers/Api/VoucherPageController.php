<?php

namespace App\Http\Controllers\Api;

use App\Classes\SEO\SeoDummyTags;
use App\Http\Controllers\Controller;
use App\Models\Websitesetting;
use App\Traits\APIRequestCommon;
use App\Traits\MetaCommon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherPageController extends Controller
{
    use APIRequestCommon;
    use MetaCommon;

    private $setting;

    public function __construct(Websitesetting $setting)
    {
        $this->setting = $setting->setting;
    }

    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $url = $request->url();
        $title = 'آلاء|طرح حکمت';
        $this->generateSeoMetaTags(new SeoDummyTags($title, 'طرح حکمت', $url, $url, route('image', [
            'category' => '11',
            'w' => '100',
            'h' => '100',
            'filename' => $this->setting->site->siteLogo,
        ]), '100', '100', null));

        $user = $request->user();
        $code = $request->get('code');

        $mobile = null;
        $isUserVerified = false;
        if (isset($user)) {
            $mobile = $user->mobile;
            $hasVerifiedMobile = $user->hasVerifiedMobile();
            if ($hasVerifiedMobile) {
                $isUserVerified = true;
            }
        }

        $data = [
            'mobile' => $mobile,
            'isUserVerified' => $isUserVerified,
            'code' => $code,
            'redirectUrl' => route('web.user.asset'),
            'verifyMobile' => true,
            'voucher' => true,
            'login' => true,
        ];

        return response()->json($data, 200);
    }
}