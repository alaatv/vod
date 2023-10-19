<?php

namespace App\Http\Controllers\Api;

use App\Classes\Updater\TextBuilderUpdater;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContentBulkEditStatusesRequest;
use App\Http\Requests\ContentBulkEditTagsRequest;
use App\Http\Requests\ContentBulkEditTextRequest;
use App\Http\Resources\Content as ContentResource;
use App\Http\Resources\ProductIndex;
use App\Models\Content;
use App\Traits\Content\ContentControllerResponseTrait;
use App\Traits\RequestCommon;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Validator;

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

    public function show(Request $request, Content $content)
    {
        if (isset($content->redirectUrl)) {
            $redirectUrl = $content->redirectUrl;
            return redirect(convertRedirectUrlToApiVersion($redirectUrl['url']),
                $redirectUrl['code'], $request->headers->all());
        }

        if (!$content->isActive()) {
            $message = '';
            $code = Response::HTTP_LOCKED;
            return response()->json([
                'message' => $message,
            ], $code);
        }

        if ($content->getCanSeeContent($request->user('api'))) {
            return response()->json($content);
        }

        $productsThatHaveThisContent = $content->activeProducts();

        return $this->getUserCanNotSeeContentJsonResponse($content, $productsThatHaveThisContent, function ($msg) {
        });
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     * @param  Content  $content
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

        if (!$content->isActive()) {
            return response()->json([], ResponseAlias::HTTP_LOCKED);
        }

        return (new ContentResource($content));
    }

    private function getContentZero()
    {
        return [
            'data' =>
                [
                    'id' => 0,
                    'redirect_url' => null,
                    'type' => 8,
                    'title' => 'test',
                    'body' => 'for test',
                    'tags' => null,
                    'file' =>
                        [
                            'video' =>
                                [
                                    0 =>
                                        [
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
                            'pamphlet' =>
                                [
                                    0 =>
                                        [
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
                    'url' =>
                        [
                            'web' => 'https://alaatv.com/c/0',
                            'api' => 'https://alaatv.com/api/v2/c/0',
                        ],
                    'previous_url' => null,
                    'next_url' => null,
                    'author' =>
                        [
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
            'message' => 'content(s) updated successfully'
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
            'message' => 'content(s) updated successfully'
        ]);
    }

    public function bulkEditStatuses(ContentBulkEditStatusesRequest $request)
    {
        $contents = Content::whereIn('id', $request->input('content_ids'));
        $contents->update($request->only('display', 'enable'));
        return response()->json([
            'message' => 'content(s) updated successfully'
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
            'message' => 'content(s) updated successfully'
        ]);
    }

    public function updateDuration(Request $request)
    {
        Validator::make($request->all(), [
            'content' => ['required'],
            'user_id' => ['required']
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
                if (isset($files) && !empty($files)) {
                    $content->file = collect($files);
                }

                if (!isset($files)) {
                    Log::channel('contentDurationApi')->info('No files made for content: '.$content->id);
                }

                $content->updateWithoutTimestamp();

                if (!$content->updateWithoutTimestamp()) {
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
}
