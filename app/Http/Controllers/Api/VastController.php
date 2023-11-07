<?php

namespace App\Http\Controllers\Api;

use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVastRequest;
use App\Http\Requests\UpdateVastRequest;
use App\Models\Vast;
use App\Traits\DateTrait;
use App\Traits\FileCommon;
use App\Traits\RequestCommon;
use App\Traits\VastTrait;

class VastController extends Controller
{
    use RequestCommon;
    use FileCommon;
    use DateTrait;
    use VastTrait;

    /**
     * VastController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.INSERT_VAST_ACCESS'))->only(['create', 'store']);
        $this->middleware('permission:'.config('constants.UPDATE_VAST_ACCESS'))->only(['edit', 'update']);
        $this->middleware('permission:'.config('constants.CHANGE_VAST_ACCESS'))->only(['create', 'store']);
        $this->middleware('permission:'.config('constants.DELETE_VAST_ACCESS'))->only(['destroy']);
    }

    public function show(Vast $vast)
    {
        $vastFileContent = Uploader::get(config('disks.VAST_XML_MINIO'), $vast->file_url);

        return response()->json([
            'data' => $vastFileContent,
            'Content-Type' => 'application/xml',
        ], 200);
    }


    public function store(StoreVastRequest $request)
    {
        // Prepare Requirements
        $videos = $request->file('videos');
        $moreInfoLink = $request->input('more_info_link');
        $clickId = $request->input('click_id');
        $clickName = $request->input('click_name');

        $uploadedVideoNames = $this->uploadVastVideos($videos);

        $vastXmlFileName = $this->generateVastXmlFileName();
        if (!$this->generationXmlFile($vastXmlFileName, $uploadedVideoNames, $moreInfoLink, $clickId, $clickName)) {
            return back()->with('error', 'ایجاد فایل وَست با خطا مواجه شده است!');
        }

        $this->requestOffsetSet($request, $uploadedVideoNames, $vastXmlFileName);
        Vast::create($request->all());

        return response()->json(['message' => 'ایجاد وَست با موفقیت انجام شد'], 200);
    }

    public function edit(Vast $vast)
    {
        return view('vast.edit',
            [
                'vast' => $vast,
                'qualities' => Vast::VIDEO_QUALITY_CAPTIONS,
            ]);
    }

    public function update(UpdateVastRequest $request, Vast $vast)
    {
        $videos = $request->input('videos', []);
        $moreInfoLink = $request->input('more_info_link');
        $clickId = $request->input('click_id');
        $clickName = $request->input('click_name');

        $vastXmlFileName = $vast->file_url;
        if (!$this->generationXmlFile($vastXmlFileName, $videos, $moreInfoLink, $clickId, $clickName)) {
            return back()->with('error', 'بروزرسانی فایل وَست با خطا مواجه شده است!');
        }


        $this->requestOffsetSet($request, $videos, $vastXmlFileName);
        $vast->update($request->all());

        return redirect()->route('web.admin.vastAdmin')->with('success', 'بروزرسانی وَست با موفقیت انجام شد.');
    }

    public function destroy(Vast $vast)
    {
        $vast->delete();
        return redirect()->route('web.admin.vastAdmin')->with('success', "وست {$vast->title} با موفقیت حذف شد");
    }
}