<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingFileRequest;
use App\Http\Requests\SettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    public function __construct()
    {
        //        $this->middleware('permission:'.config('constants.STORE_SETTING'))->only(['store', 'file']);
        //        $this->middleware('permission:'.config('constants.UPDATE_SETTING'))->only(['update']);
        //        $this->middleware('permission:'.config('constants.DESTROY_SETTING'))->only(['destroy']);
        //        $this->middleware('permission:'.config('constants.INDEX_SETTING'))->only(['index']);
    }

    public function index(Request $request)
    {
        $settings = Setting::where('service_id', $request->get('service_id', 1))->paginate();

        return SettingResource::collection($settings);
    }

    public function store(SettingRequest $request)
    {
        $data = $request->validated();
        $data['service_id'] = $request->get('service_id', 1);
        $setting = Setting::create($data);

        return response()->json($setting);
    }

    public function UserStore(Request $request)
    {
        $setting = Setting::create([
            'key' => $request->input('key'),
            'value' => $request->input('value'),
            'service_id' => 3,
        ]);

        return response()->json($setting);
    }

    public function update(SettingRequest $request, Setting $setting)
    {
        if ($setting->update($request->validated())) {
            return response()->json([
                'data' => [
                    'key' => $setting->key,
                    'message' => 'successfully updated',
                ],
            ]);
        }

        return response()->json([
            'data' => [
                'key' => $setting->key,
                'message' => 'has error while updating',
            ],
        ]);

    }

    public function destroy(Setting $setting)
    {
        if ($setting->delete()) {
            return response()->json([
                'data' => [
                    'key' => $setting->key,
                    'message' => 'successfully deleted',
                ],
            ]);
        }

        return response()->json([
            'data' => [
                'key' => $setting->key,
                'message' => 'has error while deleting',
            ],
        ]);
    }

    public function show(Request $request)
    {
        $setting = Setting::where('key', $request->setting)->where('service_id',
            $request->get('service_id', 1))->first();

        return new SettingResource($setting);
    }

    public function file(SettingFileRequest $request)
    {
        $file = $request->file('file');
        $fileName = Uploader::makeFolderName().'/'.Carbon::now()->getTimestamp().makeRandomOnlyAlphabeticalString(4).'.'.$file->getClientOriginalExtension();
        if (Uploader::put($file, config('disks.ALAA_PAGES'), null, $fileName)) {
            return response()->json([
                'data' => [
                    'url' => Uploader::url(config('disks.ALAA_PAGES'), $fileName, false),
                ],
            ]);
        }

        return myAbort(Response::HTTP_INTERNAL_SERVER_ERROR, 'آپلود با خطا مواجه شد');
    }
}
