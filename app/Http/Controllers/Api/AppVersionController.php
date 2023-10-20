<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

class AppVersionController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'android' => [
                'last_version' => 80,
                'type' => [
                    'code' => 1,
                    'hint' => 'force',
                ],
                'url' => [
                    'play_store' => 'https://play.google.com/store/apps/details?id=ir.sanatisharif.android.konkur96',
                    'bazaar' => 'https://play.google.com/store/apps/details?id=ir.sanatisharif.android.konkur96',
                    'direct' => '',
                ],
            ],
            'ios' => [
                'last_version' => 2,
                'type' => [
                    'code' => 2,
                    'hint' => 'optional',
                ],
                'url' => [
                    'app_store' => '',
                    'direct' => '',
                ],
            ],
        ]);
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function showV2(Request $request)
    {
        return response()->json([
            'android' => [
                'last_version' => 80,
                'type' => [
                    'code' => 1,
                    'hint' => 'force',
                ],
                'url' => [
                    'play_store' => 'https://play.google.com/store/apps/details?id=ir.sanatisharif.android.konkur96',
                    'bazaar' => '',
                    'direct' => 'https://nodes.alaatv.com/upload/android_app/alaatv-V7.0.1-beta.apk',
                ],
            ],
            'ios' => [
                'last_version' => 2,
                'type' => [
                    'code' => 2,
                    'hint' => 'optional',
                ],
                'url' => [
                    'app_store' => '',
                    'direct' => '',
                ],
            ],
            'web' => [
                'last_version' => '1.0.0',
                'type' => [
                    'code' => 1,
                    'hint' => 'force',
                ],
            ],
        ]);
    }
}
