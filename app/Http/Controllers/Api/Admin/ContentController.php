<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\ContentSearch;
use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContentDeleteRequest;
use App\Http\Requests\UpdateContentRequest;
use App\Http\Resources\Admin\ContentResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Content;
use App\Traits\Content\ContentControllerResponseTrait;
use App\Traits\RequestCommon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ContentController extends Controller
{
    use ContentControllerResponseTrait;
    use RequestCommon;

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_EDUCATIONAL_CONTENT_ACCESS'), ['only' => ['index',]]);
        $this->middleware('permission:'.config('constants.COPY_EDUCATIONAL_CONTENT'), ['only' => ['copy',],]);
    }

    /**
     * @param  Request  $request
     * @param  ContentSearch  $contentSearch
     * @return ResourceCollection
     */
    public function index(Request $request, ContentSearch $contentSearch)
    {
        $filters = $request->all();
        $pageName = Content::INDEX_PAGE_NAME;
        $contentSearch->setPageName($pageName);
        if ($request->has('length')) {
            $contentSearch->setNumberOfItemInEachPage($request->get('length'));
        }

        $results = $contentSearch->getAllResults($filters);
        return ContentResource::collection($results);
    }

    public function show(Content $content)
    {
        return (new \App\Http\Resources\Content($content));
    }

    public function update(UpdateContentRequest $request, Content $content)
    {
        $contentsetId = $request->input('contentset_id');

        $fileName = isset($content->file) ? basename($content->file_for_admin[$content->contenttype->name]?->first()?->fileName) : null;
        if (isset($fileName)) {
            $thumbnailFileName = pathinfo($fileName, PATHINFO_FILENAME).'.jpg';
        }

        if (!$content->isPamphlet()) {
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $this->getRequestFile($request->all(), 'thumbnail');
                if (!isset($thumbnailFileName)) {
                    $thumbnailFileName = $thumbnailFile->getClientOriginalName();
                }


                if (Uploader::put(File::get($thumbnailFile), config('disks.CONTENT_THUMBNAIL_MINIO'),
                    fileName: "$contentsetId/$thumbnailFileName")) {
                    $webpFile = pathinfo($fileName, PATHINFO_FILENAME).'.webp';
                    Uploader::delete(config('disks.CONTENT_THUMBNAIL_MINIO'), "{$content->contentset_id}/$webpFile");
                }
            }

            if (isset($thumbnailFileName)) {
                $content->thumbnail = $this->makeContentThumbnailStd($contentsetId, $thumbnailFileName);
            }
        }

        $this->fillContentFromRequest($request->all(), $content);
        if ($request->has('order') && $content->contentset_id) {
            $this->setContnetOrder($content, $request->input('order'));
        }
        if ($content->update()) {
            return response()->json([
                'success' => 'اصلاح محتوا با موفقیت انجام شد',
            ]);
        }

        return response()->json([
            'error' => 'خطای پایگاه داده',
        ]);
    }

    private function makeContentThumbnailStd(?int $contentSetId, string $fileName): ?array
    {
        if (is_null($contentSetId)) {
            return null;
        }

        return $this->makeThumbnailFile($fileName);
    }

    public function copy(Request $request, Content $content)
    {
        if (!$content->isFree && $content->contenttype_id != config('constants.CONTENT_TYPE_VIDEO')) {
            return response([
                'message' => 'تنها محتوا های رایگان ویدوئویی قابلیت کپی شدن دارند'
            ]);
        }

        $setId = $request->get('set_id');
        $name = $request->get('name');
        $description = $request->get('description');

        $newContent = $content->replicate();
        $newContent->contentset_id = $setId ?? $content->contentset_id;
        $newContent->name = $name ?? $content->name;
        $newContent->description = $description ?? $content->description;
        $newContent->copied_from = $content->id;

        $newContent->save();

        return new ContentResource($newContent);
    }

    public function destroy(ContentDeleteRequest $request)
    {
        Content::whereIn('id', $request->input('contents'))->delete();
        return response()->json(['message' => 'محتواها با موفقیت حذف شدند.']);
    }
}
