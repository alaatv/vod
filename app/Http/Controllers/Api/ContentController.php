<?php

namespace App\Http\Controllers\Api;

use App\Classes\Updater\TextBuilderUpdater;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CopyTimepointsRequest;
use App\Http\Requests\ContentBulkEditStatusesRequest;
use App\Http\Requests\ContentBulkEditTagsRequest;
use App\Http\Requests\ContentBulkEditTextRequest;
use App\Http\Requests\UpdateContentSetRequest;
use App\Http\Resources\Content as ContentResource;
use App\Http\Resources\ProductIndex;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\Contenttype;
use App\Models\DanaContentTransfer;
use App\Models\DanaProductContentTransfer;
use App\Models\DanaProductTransfer;
use App\Models\Product;
use App\Models\Timepoint;
use App\Models\User;
use App\Traits\Content\ContentControllerResponseTrait;
use App\Traits\RequestCommon;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ContentController extends Controller
{
    use ContentControllerResponseTrait;
    use RequestCommon;

    public function __construct()
    {
        $this->callMiddlewares();
    }

    private function callMiddlewares(): void
    {
        $this->middleware('permission:'.config('constants.EDIT_EDUCATIONAL_CONTENT'), [
            'only' => [
                'updateV2',
                'bulkEditText',
                'bulkEditStatuses',
                'bulkEditTags',
            ],
        ]);
    }

    /**
     * API Version 2
     *
     * @param $content1
     * @return ContentResource|JsonResponse|RedirectResponse|Redirector
     */
    public function showV2(Request $request, Content $content)
    {
        if ($content->id == 0) {
            return response()->json($this->getContentZero());
        }
        if (isset($content->redirectUrl)) {
            $redirectUrl = $content->redirectUrl;

            return redirect(convertRedirectUrlToApiVersion($redirectUrl['url'], '2'),
                $redirectUrl['code'], $request->headers->all());
        }

        if (! $content->isActive()) {
            return response()->json([], ResponseAlias::HTTP_LOCKED);
        }

        return new ContentResource($content);
    }

    private function getContentZero()
    {
        return [
            'data' => [
                'id' => 0,
                'redirect_url' => null,
                'type' => 8,
                'title' => 'test',
                'body' => 'for test',
                'tags' => null,
                'file' => [
                    'video' => [
                        0 => [
                            'link' => 'https://alaatv.com/hls/input.m3u8',
                            'ext' => 'm3u8',
                            'size' => null,
                            'caption' => 'auto-hls',
                            'res' => 'auto-hls',
                        ],
                        //                                    1 =>
                        //                                        [
                        //                                            'link' => 'https://nodes.alaatv.com/media/791/hq/791002pdnh.mp4',
                        //                                            'ext' => 'mp4',
                        //                                            'size' => NULL,
                        //                                            'caption' => '480p',
                        //                                            'res' => '480p',
                        //                                        ],
                        //                                    2 =>
                        //                                        [
                        //                                            'link' => 'https://nodes.alaatv.com/media/791/240p/791002pdnh.mp4',
                        //                                            'ext' => 'mp4',
                        //                                            'size' => NULL,
                        //                                            'caption' => '240p',
                        //                                            'res' => '240p',
                        //                                        ],
                    ],
                    'pamphlet' => [
                        0 => [
                            'link' => 'https://paid.alaatv.com/public/c/pamphlet/Hesaban_Khordad.pdf',
                            'ext' => 'pdf',
                            'size' => null,
                            'caption' => 'pdf',
                        ],
                    ],
                ],
                'duration' => null,
                'photo' => 'https://nodes.alaatv.com/media/thumbnails/791/791002pdnh.jpg',
                'is_free' => 1,
                'order' => 2,
                'page_view' => null,
                'created_at' => '2020-04-14 07:39:51',
                'updated_at' => '2020-06-01 12:59:58',
                'url' => [
                    'web' => 'https://alaatv.com/c/0',
                    'api' => 'https://alaatv.com/api/v2/c/0',
                ],
                'previous_url' => null,
                'next_url' => null,
                'author' => [
                    'id' => 37226,
                    'first_name' => 'mehdi',
                    'last_name' => 'amini rad',
                    'photo' => 'https://nodes.alaatv.com/upload/images/profile/amini_mehdi_20191211091357.jpg?w=100&h=100',
                ],
                'set' => null,
                'related_product' => null,
                'can_see' => 1,
                'source' => null,
            ],
        ];
    }

    public function products(Content $content)
    {
        $originalContentProducts = $content->set->products;
        $sampleContentsProducts = $content->related_products;

        return ProductIndex::collection($originalContentProducts->merge($sampleContentsProducts));

    }

    public function bulkUpdate(ContentBulkEditStatusesRequest $request)
    {
        $contents = Content::whereIn('id', $request->input('content_ids'));
        $contents->update($request->only('display', 'enable', 'validSince'));
        Cache::tags(['product', 'set'.'_sets', 'userAsset'])->flush();

        return response()->json([
            'message' => 'content(s) updated successfully',
        ]);
    }

    public function bulkEditText(ContentBulkEditTextRequest $request, TextBuilderUpdater $textBuilderUpdater)
    {
        $contents = Content::whereIn('id', $request->input('content_ids'));
        $textBuilderUpdater->setQueryBuilder($contents)
            ->setColumn($request->input('column'))
            ->setOperation($request->input('operation'))
            ->apply($request->input('text'), $request->input('replacing_text'));

        return response()->json([
            'message' => 'content(s) updated successfully',
        ]);
    }

    public function bulkEditStatuses(ContentBulkEditStatusesRequest $request)
    {
        $contents = Content::whereIn('id', $request->input('content_ids'));
        $contents->update($request->only('display', 'enable'));

        return response()->json([
            'message' => 'content(s) updated successfully',
        ]);
    }

    public function bulkEditTags(ContentBulkEditTagsRequest $request)
    {
        $contents = Content::whereIn('id', $request->input('content_ids'))->get();
        foreach ($contents as $content) {
            $tags = json_decode(json_encode($content->forrest_tree_tags ?? null));
            if ($request->input('operation') === 'add') {
                if (is_null($tags)) {
                    $tags = $request->input('tags');
                } else {
                    $tags = array_unique(array_merge($tags, $request->input('tags')));
                }
            } elseif ($request->input('operation') === 'delete') {
                $deletingTags = $request->input('tags');
                foreach ($deletingTags as $tag) {
                    if (isset($tags) && in_array($tag, $tags)) {
                        $key = array_search($tag, $tags);
                        unset($tags[$key]);
                    }
                }
            } else {
                $replacingTag = $request->input('replacing_tag');
                if (isset($tags) && in_array($replacingTag, $tags)) {
                    $key = array_search($replacingTag, $tags);
                    unset($tags[$key]);
                    $tags[$key] = $request->input('tag');
                }
            }
            $content->forrest_tree_tags = isset($tags) ? array_values($tags) : null;
            if ($content->isDirty()) {
                $content->save();
            }
        }

        return response()->json([
            'message' => 'content(s) updated successfully',
        ]);
    }

    public function updateDuration(Request $request)
    {
        Validator::make($request->all(), [
            'content' => ['required'],
            'user_id' => ['required'],
        ])->validate();

        $contentsArray = json_decode($request->get('content'));
        $userId = $request->get('user_id');
        Log::channel('contentDurationApi')->info('Request received, user: '.$userId);

        if (is_null($contentsArray)) {
            Log::channel('contentDurationApi')->info('Invalid contents json');

            return response()->json(['message' => 'ok']);
        }

        foreach ($contentsArray as $contentStd) {
            $contentSetId = $contentStd->set_id;
            $fileName = $contentStd->file_name;
            $contents = Content::query()->where('contentset_id', $contentSetId)->where('file', 'like',
                '%'.$fileName.'%')->get();

            if ($contents->isEmpty()) {
                Log::channel('contentDurationApi')->info('Empty contents collection');

                continue;
            }

            foreach ($contents as $content) {
                $content->duration = $content->getContentDuration();

                $files = $this->setContentFileSize($content);
                if (isset($files) && ! empty($files)) {
                    $content->file = collect($files);
                }

                if (! isset($files)) {
                    Log::channel('contentDurationApi')->info('No files made for content: '.$content->id);
                }

                $content->updateWithoutTimestamp();

                if (! $content->updateWithoutTimestamp()) {
                    Log::channel('contentDurationApi')->info('Database error on updating content: '.$content->id);
                }
            }
        }

        return response()->json(['message' => 'Done']);
    }

    public function fetchContents(Request $request)
    {
        $since = $request->get('timestamp');

        $contents = Content::active()->free()->type(config('constants.CONTENT_TYPE_VIDEO'));
        if ($since !== null) {
            $contents->where(function ($q) use ($since) {
                $q->where('created_at', '>=', Carbon::createFromTimestamp($since))
                    ->orWhere('updated_at', '>=', Carbon::createFromTimestamp($since));
            });
        }
        $contents->orderBy('created_at', 'DESC');
        $contents = $contents->paginate(25, ['*'], 'page');

        $items = [];
        foreach ($contents as $key => $content) {
            $items[$key]['id'] = $content->id;
            $items[$key]['type'] = 'content';
            $items[$key]['name'] = $content->name;
            $items[$key]['link'] = $content->url;
            $items[$key]['image'] = $content->thumbnail;
            $items[$key]['tags'] = $content->tags;
        }

        $currentPage = $contents->currentPage();
        $nextPageUrl = null;
        if ($currentPage < 40) {
            $nextPageUrl = $contents->nextPageUrl();
        }

        $contents->appends([$request->input()]);
        $pagination = [
            'current_page' => $currentPage,
            'next_page_url' => $nextPageUrl,
            'last_page' => 40,
            'data' => $items,
        ];

        return response()->json($pagination, ResponseAlias::HTTP_OK, [], JSON_UNESCAPED_SLASHES);
    }

    private function makeContentThumbnailStd(?int $contentSetId, string $fileName): ?array
    {
        if (is_null($contentSetId)) {
            return null;
        }

        return $this->makeThumbnailFile($fileName);
    }

    public function indexPendingDescriptionContent(Request $request): JsonResponse
    {
        $contents = Content::whereNotNull('tmp_description')->paginate(10, ['*']);

        return response()->json(['contents' => $contents]);
    }

    public function transferToDanaInfo($contentId)
    {
        $danaContents = DanaProductContentTransfer::where('educationalcontent_id', $contentId)->get();
        $insertType = 2;

        if ($danaContents->isEmpty()) {
            $danaContents = DanaContentTransfer::where('educationalcontent_id', $contentId)->get();
            $insertType = 1;
        }

        if ($danaContents->isEmpty()) {
            return response()->json(['error' => 'محتوا یافت نشد'], 404);
        }

        return response()->json(['danaContents' => $danaContents, 'insertType' => $insertType]);
    }

    public function copyTimepoints(CopyTimepointsRequest $request, Content $destinationContent)
    {
        $contentId = $request->input('content_id');
        $sourceContent = Content::find($contentId);
        $insertorId = $request->user()?->id;

        try {
            foreach ($sourceContent->timepoints as $timepoint) {
                $title = $timepoint->title;
                $time = $timepoint->time;
                $photo = $timepoint->photo;

                Timepoint::withTrashed()->updateOrCreate(
                    [
                        'insertor_id' => $insertorId,
                        'content_id' => $destinationContent->id,
                        'title' => $title,
                    ],
                    ['time' => $time, 'photo' => $photo]
                )->restore();
            }
        } catch (QueryException $e) {
            return response()->json(['error' => 'کپی زمانکوب ها با خطا مواجه شد'], 500);
        }

        Cache::tags(['content_'.$destinationContent->id, 'content_'.$destinationContent->id.'_timepoints'])->flush();

        return response()->json(['success' => 'کپی زمانکوب ها با موفقیت انجام شد']);
    }

    public function updateSet(UpdateContentSetRequest $request, Content $content)
    {
        $newContetnsetId = $request->get('newContetnsetId');
        $newFileFullName = $request->get('newFileFullName');
        $contentTypeId = $content->contenttype_id;
        $contentsetId = $content->contentset_id;

        if ($newContetnsetId != $content->contentset_id) {
            $contentsetId = $newContetnsetId;
        }

        if (! isset($newFileFullName)) {
            $newFileFullName = basename(optional(optional($content->file_for_admin[$content->contenttype->name])->first())->fileName);
        }

        // Get the video qualities from the content
        $qualities = [];
        foreach ($content->getVideos() as $video) {
            $qualities[$video->res] = '1';
        }
        // Setting default qualities, considering 720p, 480p, and 240p
        $qualities = ['720p' => '1', '480p' => '1', '240p' => '1'];

        // Make the files array
        $files = $this->makeContentFilesArray($contentTypeId, $contentsetId, $newFileFullName, $content->isFree,
            $qualities);

        if ($content->contenttype_id !== Content::CONTENT_TYPE_PAMPHLET) {
            $thumbnailFileName = pathinfo(parse_url($newFileFullName)['path'], PATHINFO_FILENAME).'.jpg';
            $thumbnail = $this->makeContentThumbnailStd($contentsetId, $thumbnailFileName);
            if (isset($thumbnail)) {
                $content->thumbnail = $thumbnail;
            }
        }

        if (! empty($files)) {
            $content->file = $this->makeFilesCollection($files);
        }

        // Flush the cache for the relevant tags
        Cache::tags([
            'content_'.$content->id,
            'set_'.$newContetnsetId,
            'set_'.$content->contentset_id,
        ])->flush();

        $content->contentset_id = $contentsetId;

        if ($content->update()) {
            return response()->json(['success' => 'تغییر نام با موفقیت انجام شد']);
        } else {
            return response()->json(['error' => 'خطا در اصلاح ست'], 500);
        }
    }

    public function uploadContent(Request $request)
    {
        $rootContentTypes = Contenttype::getRootContentType();
        $contentsets = Contentset::latest()->pluck('name', 'id');
        $authors = User::getTeachers()->pluck('full_name', 'id');

        return response()->json([
            'rootContentTypes' => $rootContentTypes,
            'contentsets' => $contentsets,
            'authors' => $authors,
        ], 200);
    }

    public function createArticle(Request $request)
    {
        $contenttypes = [8 => 'فیلم', 1 => 'جزوه'];

        $setId = $request->get('set');
        $set = Contentset::find($setId);
        $lastContent = null;

        if (isset($set)) {
            $lastContent = $set->getLastContent();
            if ($request->expectsJson()) {
                return response()->json([
                    'lastContent' => $lastContent,
                    'set' => $set,
                ], 200);
            }
        } elseif (isset($setId)) {
            return response()->json(['error' => 'ست مورد نظر شما یافت نشد'], 404);
        }

        return response()->json([
            'contenttypes' => $contenttypes,
            'lastContent' => $lastContent,
        ], 200);
    }

    public function transferToDana(Request $request, Content $content)
    {
        if (! $content->isActive()) {
            return response()->json(['error' => 'نمی توانید کانتنت غیرفعال را منتقل کنید'], 400);
        }

        if (! is_null($content->redirectUrl)) {
            return response()->json(['error' => 'نمی توانید کانتنت ریدایرکت شده را منتقل کنید'], 400);
        }

        $setProductIds = $content?->set->products->pluck('id');
        $foriatIds = array_merge(Product::ALL_FORIYAT_110_PRODUCTS, [Product::ARASH_TETA_SHIMI, Product::TETA_ADABIAT]);
        $productIntersect = $setProductIds->intersect($foriatIds)->all();

        if (! empty($productIntersect)) {
            return $this->transferToDanaTypeOne($content);
        } else {
            if (! DanaProductTransfer::whereIn('product_id', $setProductIds->toArray())->where('insert_type',
                2)->exists()) {
                return response()->json(['error' => 'برای ست این محتوا دوره ای ایجاد نشده است'], 404);
            }

            return $this->transferToDanaTypeTwo($content);
        }
    }
}
