<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditWebsiteFaqRequest;
use App\Http\Requests\Request;
use App\Http\Requests\UserWebsiteSettingRequest;
use App\Http\Resources\WebsiteSettingResource;
use App\Models\Websitesetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

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

    public function showFaq(Websitesetting $setting)
    {
        $faqs = $setting->faq;
        return response()->json(compact('faqs'));
    }

    public function updateFaq(EditWebsiteFaqRequest $request, Websitesetting $setting)
    {
        $photo = $this->storeFAQPhoto($request);

        $faqs = $setting->faq;
        $faqId = $request->get('faq_id');
        if (isset($faqId)) {
            [$faqKey, $faq] = $setting->findFAQ($faqId);
            $faq = Websitesetting::fillFAQ($faq, [
                'title' => $request->get('title'),
                'body' => $request->get('body'),
                'photo' => isset($photo) ? $photo : $faq->photo,
                'video' => $request->get('video'),
                'order' => $request->get('order', 0),
            ]);
            $faqs[$faqKey] = $faq;
        } else {
            $faq = Websitesetting::createFAQ([
                'id' => $setting->getLastFaqId() + 1,
                'title' => $request->get('title'),
                'body' => $request->get('body'),
                'photo' => $photo,
                'video' => $request->get('video'),
                'order' => $request->get('order', 0),
            ]);

            $faqs[] = $faq;
        }
        $updateFaq = $setting->update(['faq' => $faqs]);

        if ($updateFaq) {
            Cache::tags(['websiteSetting', 'websiteSetting_'.$setting->id])->flush();
            return response()->json(['message' => 'FAQ successfully saved'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Database error in saving FAQ'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function editFaq(Request $request, Websitesetting $setting, $settingId, $faqId)
    {
        $faqs = $setting->faq;
        $faqKey = array_search($faqId, array_column($faqs, 'id'));
        if ($faqKey === false) {
            return response()->json(['message' => 'FAQ not found'], Response::HTTP_NOT_FOUND);
        }

        $faq = $faqs[$faqKey];
        return response()->json(['faq' => $faq], Response::HTTP_OK);
    }

    public function destroyFaq(Request $request, Websitesetting $setting, $settingId, $faqId)
    {
        $faqs = $setting->faq;
        $faqKey = array_search($faqId, array_column($faqs, 'id'));
        if ($faqKey === false) {
            return response()->json(['message' => 'No FAQ found'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        unset($faqs[$faqKey]);
        $faqs = array_values($faqs);

        $updateFaq = $setting->update(['faq' => $faqs]);

        if ($updateFaq) {
            Cache::tags(['websiteSetting', 'websiteSetting_'.$setting->id])->flush();
            return response()->json(['message' => 'FAQ successfully deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
