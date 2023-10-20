<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserWebsiteSettingRequest;
use App\Http\Resources\WebsiteSettingResource;

class WebsiteSettingController extends Controller
{
    public function storeUserSetting(UserWebsiteSettingRequest $request)
    {
        $user = auth()->user();
        $userSetting = $user->websiteSetting;
        if (isset($userSetting)) {
            $userSetting->update([
                'setting' => json_encode($request->input('setting')),
            ]);
            return response()->json([
                'message' => 'setting updated successfully',
            ]);
        }
        $user->websiteSetting()->create([
            'setting' => json_encode($request->input('setting')),
        ]);
        return response()->json([
            'message' => 'setting created successfully',
        ]);
    }

    public function userSetting()
    {
        return new WebsiteSettingResource(auth()->user()->websiteSetting);
    }
}
