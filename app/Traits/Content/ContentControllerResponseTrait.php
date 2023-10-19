<?php


namespace App\Traits\Content;


use App\Collection\ProductCollection;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\TagGroup;
use App\Repositories\TagRepo;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use stdClass;


trait ContentControllerResponseTrait
{

    public function getContentSearchFilterData()
    {
        $tags = TagRepo::getAllEnableTagsBuilder()->get();

        $educationalSystem = [];
        $grade = [];
        $major = [];
        $lesson = [];
        $teacher = [];
        $tree = [];
        foreach ($tags as $tag) {
            switch ($tag->tag_group_id) {
                case TagGroup::EDUCATIONAL_SYSTEM_ID:
                    $educationalSystem[] = [
                        'name' => $tag->name,
                        'value' => $tag->value,
                        'maghtaKey' => $tag->key,
                    ];
                    break;
                case TagGroup::GRADE_ID:
                    $grade[] = [
                        'name' => $tag->name,
                        'value' => $tag->value,
                    ];
                    break;
                case TagGroup::MAJOR_ID:
                    $major[] = [
                        'name' => $tag->name,
                        'value' => $tag->value,
                        'lessonKey' => $tag->key,
                    ];
                    break;
                case TagGroup::LESSON_ID:
                    $lesson[] = [
                        'name' => $tag->name,
                        'value' => $tag->value,
                    ];
                    break;
                case TagGroup::TEACHER_ID:
                    $teacher[] = [
                        'lastName' => $tag->name,
                        'firstName' => $tag->name,
                        'value' => $tag->value,
                    ];
                    break;
                case TagGroup::TREE_ID:
                    $tree[] = [
                        'name' => $tag->name,
                        'value' => $tag->value,
                    ];
                    break;
                default:
                    break;
            }
        }

        $items = (object) [
            'nezam' => json_decode(json_encode($educationalSystem)),
            'allMaghta' => json_decode(json_encode($grade)),
            'major' => json_decode(json_encode($major)),
            'allLessons' => json_decode(json_encode($lesson)),
            'lessonTeacher' => [
                'همه_دروس' => json_decode(json_encode($teacher)),
            ],
        ];

        return json_encode($items);
    }

    public function setContnetOrder(Content $content, int|string $order)
    {
        $contents = Content::where('contentset_id', $content->contentset_id)->orderByDesc('order');
        if ($order === 'last') {
            $content->order = ++$contents->first()->order;
        } elseif (is_numeric($order)) {
            $contents->where('order', '>=', $order)->increment('order');
            $content->order = $order;
        }
    }

    /**
     * @param  Request  $request
     * @param  Content  $content
     *
     * @param  string  $guard
     *
     * @return bool
     */
    protected function userCanSeeContent(Request $request, Content $content, string $guard): bool
    {
        return $content->isFree || optional($request->user($guard))->hasContent($content);
    }

    protected function getUserCanNotSeeContentJsonResponse(
        Content $content,
        ProductCollection $productsThatHaveThisContent,
        callable $callback
    ): JsonResponse {
        $product_that_have_this_content_is_empty = $productsThatHaveThisContent->isEmpty();

        $messageLookupTable = [
            '0' => trans('content.Not Free'),
            '1' => trans('content.Not Free And you can\'t buy it'),
        ];
        $message = Arr::get($messageLookupTable, (int) $product_that_have_this_content_is_empty);

        $callback($message);
        return $this->userCanNotSeeContentResponse($message,
            Response::HTTP_FORBIDDEN, $content, $productsThatHaveThisContent, true);
    }

    /**
     * @param                                     $message
     * @param  int  $code
     *
     * @param  Content  $content
     * @param  ProductCollection  $productsThatHaveThisContent
     * @param  bool  $productInResponse
     *
     * @return JsonResponse
     */
    protected function userCanNotSeeContentResponse(
        string $message,
        int $code,
        Content $content,
        ProductCollection $productsThatHaveThisContent = null,
        bool $productInResponse = false
    ): JsonResponse {
        if (!$productInResponse) {
            return response()->json(['message' => $message,], $code);
        }
        return response()->json([
            'message' => $message,
            'content' => $content->makeHidden('file'),
            'product' => isset($productsThatHaveThisContent) && $productsThatHaveThisContent->isEmpty() ? null :
                $productsThatHaveThisContent,
        ], $code);
    }

    /**
     * @param $thumbnailUrl
     *
     * @return array
     */
    private function makeThumbnailFile($fileName): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'disk' => config('disks.CONTENT_THUMBNAIL_MINIO'),
            'fileName' => basename($fileName),
            'size' => null,
            'caption' => null,
            'res' => null,
            'type' => 'thumbnail',
            'ext' => pathinfo($fileName, PATHINFO_EXTENSION),
        ];
    }

    private function makeContentFilesArray(
        int $contentTypeId,
        ?int $contentSetId,
        ?string $fileName,
        int $isFree,
        array $quality
    ): ?array {
        if (!$isFree) {
            $productId = Contentset::find($contentSetId)?->products?->first()?->id;
            if (is_null($productId)) {
                return null;
            }

            if ($contentTypeId == config('constants.CONTENT_TYPE_VIDEO')) {
                return $this->makePaidVideoFiles($fileName, $productId, $quality);
            } elseif ($contentTypeId == config('constants.CONTENT_TYPE_PAMPHLET')) {
                return $this->makePaidPamphletFiles($fileName, $productId);
            } elseif ($contentTypeId == config('constants.CONTENT_TYPE_VOICE')) {
                return $this->makePaidVoiceFiles($fileName, $productId);
            }
        }

        if ($contentTypeId == config('constants.CONTENT_TYPE_VIDEO')) {
            return $this->makeFreeVideoFiles($fileName, $contentSetId, $quality);
        } elseif ($contentTypeId == config('constants.CONTENT_TYPE_PAMPHLET')) {
            return $this->makeFreePamphletFiles($fileName, $contentSetId);
        } elseif ($contentTypeId == config('constants.CONTENT_TYPE_VOICE')) {
            return $this->makeFreeVoiceFiles($fileName);
        }

        return [];
    }

    /**
     * @param  string  $fileName
     * @param  int  $productId
     * @param  array  $quality
     *
     * @return array
     */
    private function makePaidVideoFiles(string $fileName, int $productId, array $quality): array
    {
        $fileUrl = [];

        $HDPartialPath = $productId.'/video/HD_720p/'.$fileName;
        if (array_has($quality, '720p')) {
            $fileUrl['720p'] = [
                'partialPath' => '/paid/'.$HDPartialPath,
            ];
        }

        $hqPartialPath = $productId.'/video/hq/'.$fileName;
        if (array_has($quality, '480p')) {
            $fileUrl['480p'] = [
                'partialPath' => b'/paid/'.$hqPartialPath,
            ];
        }

        $_240pPartialPath = $productId.'/video/240p/'.$fileName;
        if (array_has($quality, '240p')) {
            $fileUrl['240p'] = [
                'partialPath' => '/paid/'.$_240pPartialPath,
            ];
        }


        $HDPartialPath = $productId.'/video/HD_720p/'.$fileName;
        if (array_has($quality, '720p')) {
            $fileUrl['720p'] = [
                'partialPath' => '/paid/'.$HDPartialPath,
                'url' => null,
            ];
        }

        $hqPartialPath = $productId.'/video/hq/'.$fileName;
        if (array_has($quality, '480p')) {
            $fileUrl['480p'] = [
                'partialPath' => '/paid/'.$hqPartialPath,
                'url' => null,
            ];
        }

        $_240pPartialPath = $productId.'/video/240p/'.$fileName;
        if (array_has($quality, '240p')) {
            $fileUrl['240p'] = [
                'partialPath' => '/paid/'.$_240pPartialPath,
                'url' => null,
            ];
        }

        return $this->makeVideoFilesArray($fileUrl, config('disks.PRODUCT_FILE_SFTP'));
    }

    /**
     * @param  array  $fileUrl
     * @param  string  $disk
     *
     * @return array
     */
    private function makeVideoFilesArray(array $fileUrl, string $disk): array
    {
        $files = [];
        foreach ($fileUrl as $key => $url) {
            $files[] = $this->makeVideoFileStdClass($url['partialPath'], $disk, $key, Arr::get($url, 'url'));
        }
        return $files;
    }

    /**
     * @param  string  $filename
     * @param  string  $disk
     * @param  string  $res
     *
     * @param  null  $url
     *
     * @param  null  $size
     *
     * @return stdClass
     */
    private function makeVideoFileStdClass(string $filename, string $disk, string $res, $url = null): stdClass
    {
        $file = new stdClass();
        $file->name = $filename;
        $file->res = $res;
        $file->caption = Content::videoFileCaptionTable()[$res];
        $file->type = 'video';
        $file->url = $url;
        $file->size = null;
        $file->disk = $disk;


        return $file;
    }

    /**
     * @param  string  $fileName
     * @param  int  $productId
     *
     * @return array
     */
    private function makePaidPamphletFiles(string $fileName, int $productId): array
    {
        $files[] = $this->makePamphletFileStdClass('/paid/'.$productId.'/'.$fileName,
            config('disks.PRODUCT_FILE_SFTP'));
        return $files;
    }

    /**
     * @param  string  $filename
     * @param  string  $disk
     *
     * @param  null  $size
     *
     * @return stdClass
     */
    private function makePamphletFileStdClass(string $filename, string $disk): stdClass
    {
        $file = new stdClass();
        $file->name = $filename;
        $file->res = null;
        $file->caption = Content::pamphletFileCaption();
        $file->url = null;
        $file->size = null;
        $file->type = 'pamphlet';
        $file->disk = $disk;

        return $file;
    }

    /**
     * @param  string  $fileName
     * @param  int  $productId
     *
     * @return array
     */
    private function makePaidVoiceFiles(string $fileName, int $productId): array
    {
        $files[] = $this->makeVoiceFileStdClass('/paid/'.$productId.'/'.$fileName, config('disks.PRODUCT_FILE_SFTP'));
        return $files;
    }

    /**
     * @param  string  $filename
     * @param  string  $disk
     * @return stdClass
     */
    private function makeVoiceFileStdClass(string $filename, string $disk): stdClass
    {
        $file = new stdClass();
        $file->name = $filename;
        $file->res = null;
        $file->caption = Content::voiceFileCaption();
        $file->url = null;
        $file->size = null;
        $file->type = 'voice';
        $file->disk = $disk;

        return $file;
    }

    /**
     * @param  string  $fileName
     * @param  int  $contentsetId
     * @param  array  $quality
     *
     * @return array
     */
    private function makeFreeVideoFiles(string $fileName, int $contentsetId, array $quality = []): array
    {
        $fileUrl = [];

        if (array_has($quality, '720p')) {
            $fileUrl['720p'] = [
                'partialPath' => basename($fileName),
            ];
        }

        if (array_has($quality, '480p')) {
            $fileUrl['480p'] = [
                'partialPath' => basename($fileName),
            ];
        }

        if (array_has($quality, '240p')) {
            $fileUrl['240p'] = [
                'partialPath' => basename($fileName),
            ];
        }

        return $this->makeVideoFilesArray($fileUrl, config('disks.FREE_VIDEO_CONTENT_MINIO'));
    }

    /**
     * @param  string  $fileName
     *
     * @return array
     */
    private function makeFreePamphletFiles(string $fileName, string $contentSetId): array
    {
        $fileName = "$contentSetId/$fileName";
        $files[] = $this->makePamphletFileStdClass($fileName, config('disks.PAMPHLET_SFTP'));
        return $files;
    }

    /**
     * @param  string  $fileName
     *
     * @return array
     */
    private function makeFreeVoiceFiles(string $fileName): array
    {
        $files[] = $this->makeVoiceFileStdClass($fileName, config('disks.VOICE_SFTP'));
        return $files;
    }

    /**
     * @param  array  $inputData
     * @param  Content  $content
     *
     * @return void
     */
    private function fillContentFromRequest(array $inputData, Content $content): void
    {
        $validSinceDateTime = Arr::get($inputData, 'validSinceDate');
        $createdAt = Arr::get($inputData, 'created_at');
        $enabled = Arr::get($inputData, 'enable', 0);
        $isContentEnable = $content->isEnable();
        $display = Arr::has($inputData, 'display') ? 1 : 0;
        $isFree = Arr::has($inputData, 'isFree') ? 1 : 0;
        $tagString = Arr::get($inputData, 'tags');
        $files = Arr::get($inputData, 'files', []);
        $pamphlet = Arr::get($inputData, 'pamphlet');
        $voice = Arr::get($inputData, 'voice');

        $redirectUrl = Arr::get($inputData, 'redirectUrl', null);
        $redirectCode = Arr::get($inputData, 'redirectCode', null);
        if (isset($redirectUrl) && isset($redirectCode)) {
            $inputData['redirectUrl'] = [
                'url' => $redirectUrl,
                'code' => $redirectCode,
            ];
        }

        $content->fill($inputData);

        if (!$content->isEnable() && $enabled) {
            $content->validSince = Carbon::now('Asia/Tehran');
        } else {
            $content->validSince = Carbon::parse($validSinceDateTime)->format('Y-m-d H:i:s');
        }

        if (isset($createdAt)) {
            $content->created_at = $createdAt;
        }

        $content->enable = $enabled;
        $content->display = $display;

        $content->isFree = $isFree;
        $content->tags = convertTagStringToArray($tagString);

        if (Arr::get($inputData, 'section_id') == 0) {
            $content->section_id = null;
        }

        if (Arr::has($inputData, 'forrest_tree')) {
            $content->forrest_tree_grid = Arr::get($inputData, 'forrest_tree');
        }

        if (isset($pamphlet)) {
            $files = $this->storePamphletOfContent($content, $pamphlet);
        }

        if (isset($voice)) {
            $files = $this->storeVoiceOfContent($content, $voice);
        }

        if (!empty($files)) {
            $content->file = $this->makeFilesCollection($files);
        }

        $content->duration = $content->getContentDuration();
        $files = $this->setContentFileSize($content);
        if (isset($files) && !empty($files)) {
            $content->file = collect($files);
        }

        if (!Arr::has($inputData, 'sources_id')) {
            return;
        }
        $sources = Source::whereIn('id', Arr::get($inputData, 'sources_id'))->get();
        if ($sources->isNotEmpty()) {
            $content->sources()->sync($sources);
        }

    }

    /**
     * @param  Content  $content
     *
     * @param  array  $files
     */
    private function makeFilesCollection(array $files): Collection
    {
        $fileCollection = collect();

        foreach ($files as $key => $file) {
            $disk = isset($file->disk) ? $file->disk : null;
            $fileName = isset($file->name) ? $file->name : null;
            $caption = isset($file->caption) ? $file->caption : null;
            $res = isset($file->res) ? $file->res : null;
            $type = isset($file->type) ? $file->type : null;
            $url = isset($file->url) ? $file->url : null;
            $size = isset($file->size) ? $file->size : null;
            if ($this->strIsEmpty($fileName)) {
                continue;
            }
            $fileCollection->push([
                'uuid' => Str::uuid()->toString(),
                'disk' => $disk,
                'url' => $url,
                'fileName' => $fileName,
                'size' => $size,
                'caption' => $caption,
                'res' => $res,
                'type' => $type,
                'ext' => pathinfo($fileName, PATHINFO_EXTENSION),
            ]);
        }

        return $fileCollection;
    }

    private function setContentFileSize(Content $content): ?array
    {
        if ($content->contenttype_id != Content::CONTENT_TYPE_VIDEO && $content->contenttype_id != Content::CONTENT_TYPE_PAMPHLET) {
            return null;
        }

        $basePath = 'http://127.0.0.1:8000';
        $files = $content->getRawOriginal('file');
        $files = json_decode($files);

        if (!is_array($files)) {
            return null;
        }
        $files2 = $content->file_for_app;
        $videos = $files2?->get('video');
        $pamphlets = $files2->get('pamphlet');

        foreach ($files as $file) {
            $size = 0;
            $fileName = optional($file)->fileName;
            if (is_null($fileName)) {
                continue;
            }
            if ($content->contenttype_id == config('constants.CONTENT_TYPE_VIDEO') && isset($videos)) {
                foreach ($videos as $video) {
                    if ($video->res == $file->res) {
                        $link = strtok($video->link, '?');
                        $parseUrl = parse_url($link);
                        $url = $basePath.$parseUrl['path'];
                        $size = url_get_size($url);
                    }
                }
            }
            if ($content->contenttype_id == config('constants.CONTENT_TYPE_PAMPHLET') && isset($pamphlets)) {
                $pamphlet = $pamphlets[0];
                $link = strtok($pamphlet->link, '?');
                $parseUrl = parse_url($link);
                $url = $basePath.$parseUrl['path'];
                $size = url_get_size($url);
            }
            $file->size = $size;

        }

        return $files;
    }
}

