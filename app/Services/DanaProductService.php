<?php

namespace App\Services;

use App\Models\DanaAuthor;
use App\Models\DanaProductContentTransfer;
use App\Models\DanaProductSetTransfer;
use App\Models\DanaProductTransfer;
use App\Models\Product;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class DanaProductService
{
    public static function createCourse($product)
    {
        $danaProductTransfer =
            DanaProductTransfer::where('product_id', $product->id)->whereNotNull('dana_course_id')->first();
        if (isset($danaProductTransfer)) {
            return $danaProductTransfer->dana_course_id;
        }
        $danaProductTransfer =
            DanaProductTransfer::updateOrCreate(['product_id' => $product->id], [
                'dana_course_id' => null, 'product_id' => $product->id, 'status' => DanaProductTransfer::TRANSFERRING,
                'insert_type' => 2
            ]);

        $widePhoto = $product?->wide_photo;
        $productPhoto = str_replace(' ', '%20', $widePhoto);
        if (is_null($widePhoto) || is_null(self::checkUrlExist($productPhoto))) {
            Log::channel('danaTransfer')->debug("In DanaProductService : product {$product->id} has no wide photos");
            session()->put('error', 'این محصول عکس عریض ندارد');
            $danaProductTransfer->update(['status' => DanaProductTransfer::NOT_TRANSFERRED]);
            return false;
        }

        $photo = self::uploadFile($productPhoto, basename($productPhoto));
        if (is_null($photo)) {
            Log::channel('danaTransfer')->debug("In DanaProductService : could not upload photo for {$product->id}");
            session()->put('error', 'مشکلی در آپلود عکس محصول در دانا رخ داده است');
            $danaProductTransfer->update(['status' => DanaProductTransfer::NOT_TRANSFERRED]);
            return false;
        }

        $introVideo = null;
        $productIntroVideo = str_replace(' ', '%20', $product?->intro_video);
        if (!is_null($product?->intro_video) && !is_null(self::checkUrlExist($productIntroVideo))) {
            $introVideo = self::uploadFile($productIntroVideo, basename($productIntroVideo));
        }

        $set = $product->sets()->first();

        if (is_null($set)) {
            Log::channel('danaTransfer')->debug('این محصول هیچ ستی ندارد');
            session()->put('error', 'این محصول هیچ ستی ندارد');
            return false;
        }

        $author = $set?->user;
        $allContents = $set->contents->whereNotNull('author_id');
        if (!isset($author)) {
            $firstContent =
                $allContents->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))->sortBy('order')->first();
            $author = $firstContent?->user;
        }
        if (!isset($author)) {
            $firstContent =
                $allContents->where('contenttype_id',
                    config('constants.CONTENT_TYPE_PAMPHLET'))->sortBy('order')->first();
            $author = $firstContent?->user;
        }
        if (!isset($author)) {
            Log::channel('danaTransfer')->debug("In DanaProductService : create course : product-{$product->id} has not teachers");
            session()->put('error', 'دبیر محصول شما مشخص نشده است');
            return false;
        }

        $danaTeacherId = self::getAuthorId($author->id);
        if (is_null($danaTeacherId)) {
            Log::channel('danaTransfer')->debug("In DanaProductService : content set {$set->id} has no dana teacher");
            $danaProductTransfer->update(['status' => DanaProductTransfer::NOT_TRANSFERRED]);
            session()->put('error', 'دبیر در دانا ثبت نشده است');
            return false;
        }

        $price = $product->basePrice;
        $groupId = 28;
        if (in_array($product->id, array_keys(Product::ALL_CHATR_NEJAT2_PRODUCTS))) {//chatr
            $groupId = 67;
        }
        if (in_array($product->id, Product::ALL_FORIYAT_110_PRODUCTS)) {//110
            $groupId = 69;
        }
        if (in_array($product->id, array_keys(Product::ALL_NAHAYI_1402_PRODUCTS))) {//nahayi
            $groupId = 77;
        }
        if (in_array($product->id, Product::ALL_EMTEHAN_NAHAYI_NOHOM_1402)) {//nahayi
            $groupId = 78;
        }

        $courseDto = [
            'GroupID' => $groupId,
            'CourseFee' => $price,
            'CourseLength' => 5,
            'InstitueID' => 1,
            'ImageID' => $photo,
            'IntroductionMovie' => $introVideo ?? $photo,
            'Name' => $product->name,
//            "NumberOfSession"     => $set->activeContents()->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))->count(),
            'NumberOfSession' => 0,
            'Status' => true,
            'Introduction' => $product->longDescription,
            'CourseUsage' => $product->usage_description,
            'CourseObjective' => $product->objective_description,
            'CourseAudience' => $product->audience_description,
            'CoursePrerequisite' => $product->prerequisite_description,
            'TeacherID' => $danaTeacherId,
            'ExamScore' => 0,
            'CourseLengthDay' => 3,
            'MovieScore ' => 100,
            'ExerciseScore' => 0,
            'OfflineSessionScore' => 0,
            'MinScore' => 50,
            'SpecialOffer' => 0,
            'HasCertificate' => 0,
            'UnLimit' => true,
            'NeedAssitant' => 0,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => '{
              "CourseDto": '.json_encode($courseDto).',
              "Tags": '.json_encode($product->tags?->tags).'
            }',
        ];

        $response = self::send('POST', '/Course/CreateCourse', $option);
        if ($response['status_code'] == 200 && Arr::get($response, 'isSuccess') == true) {
            $danaProductTransfer->update([
                'dana_course_id' => $response['key'], 'product_id' => $product->id,
                'status' => DanaProductTransfer::SUCCESSFULLY_TRANSFERRED
            ]);
            session()->put('success', 'محصول با موفقیت منتقل شد');
            return $response['key'];
        }
        $danaProductTransfer->update(['status' => DanaProductTransfer::FAILED_TRANSFER]);
        Log::channel('danaTransfer')->debug("In DanaProductService : request to dana API for creating course {$set->id} was not successful , response status : ".$response['status_code'].' - '.Arr::get($response,
                'error'));
        session()->put('error', ' : خطایی در ریکوئست به دانا رخ داد'.$response['status_code']);
        return false;
    }

    public static function checkUrlExist($url)
    {
        $headers = get_headers($url);
        if (substr($headers[0], 9, 3) != '200') {
            return null;
        }
        return true;
    }

    public static function uploadFile($file, $name, $type = 'image')
    {
        $path = self::createPath($file, $name);
        self::deleteFile($name, $path);

        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
            ],
            'multipart' => [
                [
                    'name' => 'path',
                    'contents' => $path,
                ],
                [
                    'name' => 'action',
                    'contents' => 'save',
                ],
                [
                    'name' => 'uploadFiles',
                    'contents' => file_get_contents($file),
                    'filename' => $name,
                ],
            ],
        ];
        $response = self::send('POST', '/CloudFile/Upload', $option);
        if ($response['status_code'] == Response::HTTP_OK or $response['status_code'] == Response::HTTP_BAD_REQUEST) {
            $response = self::getFileId($path.$name);
            if (!in_array($response['status_code'], [Response::HTTP_OK, Response::HTTP_BAD_REQUEST])) {
                Log::channel('danaTransfer')->debug('In DanaProductService : uploadFile : file was uploaded but not found');
                return null;
            }

            return $response['result'];
        }

        Log::channel('danaTransfer')->debug('In DanaProductService : uploadFile : file was not uploaded');
        return null;
    }

    public static function createPath($path, $name)
    {
        $path = substr($path, 24, strpos($path, $name) - 24);
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => '{
"action": "create",
  "path": "'.$path.'",
  "name": ""
}',
        ];
        $response = self::send('POST', '/CloudFile/FileOperations', $option);
        if ($response['status_code'] != 200) {
            return null;
        }
        return $path;
    }

    public static function send($method, $url, $option, $useFullUrl = false)
    {
        try {
            $client = new Client();
            $response = $client->request(
                $method,
                $useFullUrl ? $url : 'https://ugcbe.danaapp.ir/api'.$url,
                $option
            );
        } catch (Exception $exception) {
            return ['status_code' => $exception->getCode()];
        }
        $data = json_decode($response->getBody(), true);
        if (!is_array($data)) {
            $data = ['result' => $data];
        }
        $data['status_code'] = $response->getStatusCode();
        return $data;
    }

    public static function deleteFile($name, $path)
    {
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'action' => 'delete',
                'names' => [$name],
                'path' => $path
            ])
        ];
        $response = self::send('POST', '/CloudFile/FileOperations', $option);
        if ($response['status_code'] == 200 and is_null($response['error'])) {
            return true;
        }
        return false;
    }

    public static function getFileId($name)
    {
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
            ],
            'query' => [
                'key' => $name,
            ],
        ];
        $response = self::send('GET', '/CloudFile/getId', $option);
        return $response;
    }

    public static function getAuthorId($id)
    {
        return DanaAuthor::where('author_id', $id)->first()?->dana_author_id;
    }

    public static function editCourse($product, $courseId, $extraDto = [])
    {
        $photo = null;
        $productPhoto = str_replace(' ', '%20', $product?->wide_photo);
        if (!is_null(self::checkUrlExist($productPhoto))) {
            $photo = self::uploadFile($productPhoto, basename($productPhoto));
        }
        if (is_null($photo)) {
            throw new Exception("In DanaProductService: editCourse : product {$product->id} has no wide photos");
        }

        $introVideo = null;
        $productIntroVideo = str_replace(' ', '%20', $product?->intro_video);
        if (!is_null(self::checkUrlExist($productIntroVideo))) {
            $introVideo = self::uploadFile($productIntroVideo, basename($productIntroVideo));
        }

        $set = $product->sets?->first();
        $author = $set?->user;
        if (!isset($author)) {
            $firstContent =
                $set->contents->where('contenttype_id',
                    config('constants.CONTENT_TYPE_VIDEO'))->sortBy('order')->first();
            $author = $firstContent?->user;
        }
        if (!isset($author)) {
            $firstContent =
                $set->contents->where('contenttype_id',
                    config('constants.CONTENT_TYPE_PAMPHLET'))->sortBy('order')->first();
            $author = $firstContent?->user;
        }
        if (!isset($author)) {
            throw new Exception("In DanaProductService: editCourse : product {$product->id} has no teacher in alaa");
        }

        $danaTeacherId = self::getAuthorId($author->id);
        if (is_null($danaTeacherId)) {
            throw new Exception("In DanaProductService: editCourse : content set {$set->id} has no dana teacher");
        }

        $groupId = 28;
        if (in_array($product->id, array_keys(Product::ALL_CHATR_NEJAT2_PRODUCTS))) {//chatr
            $groupId = 67;
        }
        if (in_array($product->id, Product::ALL_FORIYAT_110_PRODUCTS)) {//110
            $groupId = 69;
        }
        if (in_array($product->id, array_keys(Product::ALL_NAHAYI_1402_PRODUCTS))) {//nahayi
            $groupId = 77;
        }
        $price = $product->price;
        $basePrice = $price['base'];
        $finalPrice = $price['final'];
        $courseDto = [
            'GroupID' => $groupId,
            'ImageID' => $photo,
            'IntroductionMovie' => $introVideo ?? $photo,
            'Name' => $product->name,
            'Status' => true,
            'Introduction' => $product->longDescription,
            'CourseUsage' => $product->usage_description,
            'CourseObjective' => $product->objective_description,
            'CourseAudience' => $product->audience_description,
            'CoursePrerequisite' => $product->prerequisite_description,
            'TeacherID' => $danaTeacherId,
        ];
        $courseDto['OldCourseFee'] = $basePrice;
        $courseDto['CourseFee'] = $finalPrice;
        if (in_array($product->id, array_keys(Product::ALL_CHATR_NEJAT2_PRODUCTS))) {
            $courseDto['CourseFee'] = 0;
        }
        if (!empty($extraDto)) {
            $courseDto = array_merge($courseDto, $extraDto);
        }

        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => '{
              "CourseId": '.$courseId.',
              "CourseDto": '.json_encode($courseDto).',
              "Tags": '.json_encode($product->tags?->tags).'
            }',
        ];

//        Log::channel('debug')->debug(json_encode($courseDto));

        $response = self::send('POST', '/Course/EditCourse', $option);
        if ($response['status_code'] == 200 && Arr::get($response, 'isSuccess') == true) {
            return $response['key'];
        }
        throw new Exception("In DanaProductService: editCourse : request to dana API for editing course {$set->id} was not successful , response status : ".$response['status_code'].' - '.Arr::get($response,
                'error'));
    }

    public static function createSession($courseId, $set, $order, $productId)
    {
        $danaProductSetTransfer = DanaProductSetTransfer::updateOrCreate([
            'contentset_id' => $set->id, 'product_id' => $productId
        ], [
            'dana_session_id' => null, 'contentset_id' => $set->id, 'product_id' => $productId,
            'status' => DanaProductSetTransfer::TRANSFERRING
        ]);
        $body = [
            'CourseId' => $courseId,
            'SessionsCount' => $order,
            'Name' => $set->small_name,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/AddCourseSession', $option);
        if ($response['status_code'] != 200) {
            $danaProductSetTransfer->update([
                'status' => DanaProductSetTransfer::FAILED_TRANSFER,
            ]);
            Log::channel('danaTransfer')->debug("In DanaProductService : request to dana API for AddCourseSession {$set->id} was not successful , response status : ".$response['status_code']);
            return false;
        }
        $danaSessionId = self::getDanaSessionId($courseId, $set->small_name);
        if (is_null($danaSessionId)) {
            Log::channel('danaTransfer')->debug("In DanaProductService : request to dana API for getting session id of {$set->id} was not successful, course id : {$courseId} , response status : ".$response['status_code']);
            return false;
        }
        $danaProductSetTransfer->update([
            'dana_session_id' => $danaSessionId, 'status' => DanaProductSetTransfer::SUCCESSFULLY_TRANSFERRED
        ]);

//        Log::channel('danaTransfer')->debug("adding files of session {$set->small_name} of course {$courseId}");
        /**
         * AddSessionContent
         */
        foreach ($set->activeContents as $content) {
            self::createContent($content, $courseId, $danaSessionId);
        }

        return $danaSessionId;
    }

    public static function getDanaSessionId($courseId, $name)
    {
        $response = self::getDanaSession($courseId);
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['name'] == $name) {
                return $row['sessionID'];
            }
        }
    }

    public static function getDanaSession($courseId)
    {
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
            ],
        ];
        return self::send('GET', '/Course/GetCourseSessionInfo/'.$courseId, $option);
    }

    public static function createContent($content, $courseId, $danaSessionId)
    {
        $danaProductContentTransfer = DanaProductContentTransfer::updateOrCreate(
            ['educationalcontent_id' => $content->id, 'dana_course_id' => $courseId],
            [
                'dana_course_id' => $courseId, 'dana_session_id' => $danaSessionId,
                'educationalcontent_id' => $content->id, 'status' => DanaProductContentTransfer::TRANSFERRING
            ]
        );
        if ($content->contenttype_id == 8) {
            $type = 1;
            /**
             * Upload videos
             */
            $files = json_decode($content->getAttributes()['file']);
            $fileId = null;
            foreach ($files as $file) {
                if ($file->res != '720p') {
                    continue;
                }
                $videoUrl = config('filesystems.disks.productFileSFTP.download_endpoint').$file->fileName;
                $fileId =
                    self::uploadFile($videoUrl, substr($file->fileName, strrpos($file->fileName, '/') + 1));
            }
            if (is_null($fileId)) {
                Log::channel('danaTransfer')->debug('In DanaProductService : '.'720p quality was not uploaded contentId '.$content->id);

                foreach ($files as $file) {
                    if ($file->res != '480p') {
                        continue;
                    }
                    $videoUrl = config('filesystems.disks.productFileSFTP.download_endpoint').$file->fileName;
                    $fileId =
                        self::uploadFile($videoUrl, substr($file->fileName, strrpos($file->fileName, '/') + 1));
                }
            }
            if (is_null($fileId)) {
                $danaProductContentTransfer->update([
                    'status' => DanaProductContentTransfer::FAILED_TRANSFER,
                ]);
                Log::channel('danaTransfer')->debug('In DanaProductService : '.'no quality was uploaded contentId '.$content->id);
                return false;
            }
        } else {
            if ($content->contenttype_id == 1) {
                $type = 3;
                $file = json_decode($content->getAttributes()['file']);
                $pamphletUrl = config('filesystems.disks.productFileSFTP.download_endpoint').$file[0]->fileName;
                $fileId = self::uploadFile($pamphletUrl, basename($pamphletUrl));
                if (is_null($fileId)) {
                    $danaProductContentTransfer->update([
                        'status' => DanaProductContentTransfer::FAILED_TRANSFER,
                    ]);
                    Log::channel('danaTransfer')->debug('In DanaProductService : '.'pamphlet was not uploaded , contentId'.$content->id);
                    return false;
                }
            }
        }
        $danaProductContentTransfer->update(['dana_filemanager_content_id' => $fileId]);

        $contentTitle = explode('-', $content->name);
        $contentTitle = Arr::get($contentTitle, 2);
        if (!isset($contentTitle)) {
            $contentTitle = $content->name;
        }
        $body = [
            'courseId' => $courseId,
            'sessionId' => $danaSessionId,
            'type' => $type,
            'isFree' => false,
            'title' => $contentTitle,
            'isDownloadable' => false,
            'contentId' => $fileId,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/AddSessionContent', $option);

        if ($response['status_code'] != 200 || $response['isSuccess'] != true) {
            $danaProductContentTransfer->update(['status' => DanaProductContentTransfer::FAILED_TRANSFER]);
            Log::channel('danaTransfer')->debug("In DanaProductService : request to dana API for creating content {$content->id} was not successful , response status : ".$response['status_code']);
            return false;
        }

        $danaProductContentTransfer->update([
            'dana_content_id' => self::getDanaSessionContentId($courseId, $danaSessionId, $contentTitle),
            'status' => DanaProductContentTransfer::SUCCESSFULLY_TRANSFERRED,
        ]);
        return true;
    }

    public static function getDanaSessionContentId($courseId, $sessionId, $name)
    {
        $response = self::getSessionContent($courseId, $sessionId);
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['name'] == $name) {
                return $row['id'];
            }
        }
    }

    public static function getSessionContent($courseId, $sessionId)
    {
        $body = [
            'courseId' => $courseId,
            'sessionId' => $sessionId,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        return self::send('POST', '/Course/GetSessionContent', $option);
    }

    public static function getDanaSessionContentIdByFileManagerId($courseId, $sessionId, $filemanagerId)
    {
        $body = [
            'courseId' => $courseId,
            'sessionId' => $sessionId,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/GetSessionContent', $option);
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['contentId'] == $filemanagerId) {
                return $row['id'];
            }
        }
    }

    public static function deleteContent($courseId, $sessionId, $filemanagerId, $type)
    {
        $body = [
            'courseId' => $courseId,
            'sessionId' => $sessionId,
            'contentId' => $filemanagerId,
            'type' => $type
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/DeleteSessiosnContent', $option);
        return $response;
    }

    public static function changeCourseStatus($courseId)
    {
//        Log::channel('danaTransfer')->debug("In DanaProductService : Changing status of dana course : {$courseId}");
        $body = [
            'courseId' => $courseId,
            'status' => 2,
            'isCourse' => true,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $result = self::send('POST', 'https://ugcbe.danaapp.ir/api/Course/ChangeCourseStatus', $option, true);
        if (Arr::get($result, 'status_code') == 200 && Arr::get($result, 'isSuccess') == true) {
            return true;
        }
        Log::channel('danaTransfer')->debug("In DanaProductService : Changing status of dana course-{$courseId} failed with status code :".Arr::get($result,
                'status_code').' - '.Arr::get($result, 'error'));
        return false;
    }

    public static function approveCourse($courseId)
    {
//        Log::channel('danaTransfer')->debug("In DanaProductService : Approving dana course : {$courseId}");
        $body = [
            'courseId' => $courseId,
            'adminStatus' => 4,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $result = self::send('POST', 'https://aggbe.danaapp.ir/api/CourseCp/ApproveCourse', $option, true);
        if (Arr::get($result, 'status_code') == 200 && Arr::get($result, 'isSuccess') == true) {
            return true;
        }

        Log::channel('danaTransfer')->debug("In DanaProductService : Approving of dana course-{$courseId} failed with status code :".Arr::get($result,
                'status_code').' - '.Arr::get($result, 'error'));
        return false;
    }

    public static function send2($method, $url, $option, $useFullUrl = false)
    {
        try {
            $client = new Client();
            $response = $client->request(
                $method,
                $useFullUrl ? $url : 'https://ugcbe.danaapp.ir/api'.$url,
                $option
            );
        } catch (Exception $exception) {
            return ['status_code' => $exception->getCode()];
        }
        $data = json_decode($response->getBody(), true);
        $data = ['result' => $data];
        $data['status_code'] = $response->getStatusCode();
        return $data;
    }

    public static function deleteSession(array $data)
    {
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data),
        ];
        $response = self::send('POST', '/Course/DeleteSessiosn', $option);
        if ($response['status_code'] != 200) {
            Log::channel('danaTransfer')->debug('In DanaProductService : session was not deleted : status is '.$response['status_code']);
            Log::channel('danaTransfer')->debug(json_encode($data));
        }

        if (isset($response['isSuccess']) && !$response['isSuccess']) {
            Log::channel('danaTransfer')->debug('In DanaProductService : session name was not updated : isSuccess is false');
            Log::channel('danaTransfer')->debug(json_encode($data));
        }
    }

    public static function updateSessionPriority(array $priorityData)
    {
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => '{
              "PriorityDtos": '.json_encode($priorityData).',
            }',
        ];
        $response = self::send('POST', '/course/UpdatePriorityOfSessions', $option);
        if ($response['status_code'] != 200) {
            Log::channel('danaTransfer')->debug('In DanaProductService : session order was not updated : status is '.$response['status_code']);
            Log::channel('danaTransfer')->debug(json_encode($priorityData));
        }

        if (isset($response['isSuccess']) && !$response['isSuccess']) {
            Log::channel('danaTransfer')->debug('In DanaProductService : session order was not updated : isSuccess is false');
            Log::channel('danaTransfer')->debug(json_encode($priorityData));
        }
    }

    public static function updateSessionName(array $sessionNameData): void
    {
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($sessionNameData),
        ];
        $response = self::send('POST', '/Course/EdiCourseSession', $option);
        if ($response['status_code'] != 200) {
            Log::channel('danaTransfer')->debug('In DanaProductService : session name was not updated : status is '.$response['status_code']);
            Log::channel('danaTransfer')->debug(json_encode($sessionNameData));
        }

        if (isset($response['isSuccess']) && !$response['isSuccess']) {
            Log::channel('danaTransfer')->debug('In DanaProductService : session name was not updated : isSuccess is false');
            Log::channel('danaTransfer')->debug(json_encode($sessionNameData));
        }
    }

    public static function editShareOfCp($courseId)
    {
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'courseId' => $courseId,
                'shareOfCp' => 0
            ]),
        ];
        $response = self::send('POST', 'https://aggbe.danaapp.ir/api/CourseCp/EditShareOfCp', $option, true);
        if ($response['status_code'] != 200) {
            throw new Exception("In DanaProductService : editShareOfCp : has error on CourseId={$courseId}");
        }

        if (isset($response['isSuccess']) && !$response['isSuccess']) {
            throw new Exception("In DanaProductService : editShareOfCp : isSuccess is false on CourseId={$courseId}");
        }
    }

    public static function createContentWithoutUpload($danaInfo, $contentId)
    {
        $danaProductContentTransfer = DanaProductContentTransfer::create(
            [
                'dana_course_id' => $danaInfo['courseId'],
                'dana_session_id' => $danaInfo['session'],
                'dana_filemanager_content_id' => $danaInfo['contentId'],
                'educationalcontent_id' => $contentId,
                'status' => DanaProductContentTransfer::TRANSFERRING
            ]
        );
        $body = [
            'courseId' => $danaInfo['courseId'],
            'sessionId' => $danaInfo['session'],
            'type' => $danaInfo['type'],
            'isFree' => $danaInfo['isFree'],
            'title' => $danaInfo['name'],
            'isDownloadable' => $danaInfo['isDownloadable'],
            'contentId' => $danaInfo['contentId'],
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/AddSessionContent', $option);

        if ($response['status_code'] != 200 || $response['isSuccess'] != true) {
            $danaProductContentTransfer->update(['status' => DanaProductContentTransfer::FAILED_TRANSFER]);
            Log::channel('danaTransfer')->debug("In DanaProductService :  createContentWithoutUpload : request to dana API for creating content {$contentId} was not successful , response status : ".$response['status_code']);
            return false;
        }

        $danaProductContentTransfer->update([
            'dana_content_id' => self::getDanaSessionContentId($danaInfo['courseId'], $danaInfo['session'],
                $danaInfo['name']),
            'status' => DanaProductContentTransfer::SUCCESSFULLY_TRANSFERRED,
        ]);
        return true;
    }
}
