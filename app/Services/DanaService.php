<?php

namespace App\Services;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Contentset;
use App\Models\DanaAuthor;
use App\Models\DanaAuthor;
use App\Models\DanaContentTransfer;
use App\Models\DanaContentTransfer;
use App\Models\DanaProductTransfer;
use App\Models\DanaProductTransfer;
use App\Models\DanaSetTransfer;
use App\Models\DanaSetTransfer;
use App\Models\DanaToken;
use App\Models\DanaToken;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class DanaService
{
    public static function createCourse($set, $product = null)
    {

        $setPhoto = $set->photo;
        $danaTransfer = DanaSetTransfer::where('contentset_id', $set->id)->first();
        if ($danaTransfer) {
            return $danaTransfer->dana_course_id;
        }

        $photo = null;
        $productPhoto = str_replace(' ', '%20', $product?->wide_photo);
        if (!isset($productPhoto)) {
            $productPhoto = str_replace(' ', '%20', $product?->photo);
        }
        if (!isset($productPhoto)) {
            $productPhoto = $setPhoto;
        }

        if (!is_null(self::checkUrlExist($productPhoto))) {
            $photo = self::uploadFile($productPhoto, basename($productPhoto));
        }
        if (is_null($photo)) {
            Log::channel('debug')->debug("In DanaService : content set {$set->id} has no photos");
            return false;
        }

        $introVideo = null;
        $productIntroVideo = str_replace(' ', '%20', $product?->intro_video);
        if (!is_null(self::checkUrlExist($productIntroVideo))) {
            $introVideo = self::uploadFile($productIntroVideo, basename($productIntroVideo));
        }

        $danaTeacherId = self::getAuthorId($set->author_id);
        if (is_null($danaTeacherId)) {
            Log::channel('danaTransfer')->debug("In DanaService : content set {$set->id} has no dana teacher");
            return false;
        }

        $price = isset($product) ? $product->basePrice : 0;

        $courseDto = [
            'GroupID' => 28,
            'CourseFee' => $price,
            'CourseLength' => 5,
            'InstitueID' => 1,
            'ImageID' => $photo,
            'IntroductionMovie' => $introVideo ?? $photo,
            'Name' => $product->name,
//            "NumberOfSession"     => $set->activeContents()->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))->count(),
            'NumberOfSession' => 0,
            'Status ' => true,
            'Introduction' => $product->longDescription,
            'CourseUsage' => '',
            'CourseObjective' => '',
            'CourseAudience' => '',
            'CoursePrerequisite' => '',
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
              "Tags": '.json_encode($set->tags?->tags).'
            }',
        ];

        $response = self::send('POST', '/Course/CreateCourse', $option);
        if ($response['status_code'] == 200) {
            $danaSet = DanaSetTransfer::create([
                'dana_course_id' => $response['key'], 'contentset_id' => $set->id,
                'status' => DanaSetTransfer::SUCCESSFULLY_TRANSFERRED
            ]);
            DanaProductTransfer::create([
                'dana_course_id' => $danaSet->dana_course_id,
                'product_id' => Contentset::find($danaSet->contentset_id)->products->first()->id,
                'status' => $danaSet->status,
                'created_at' => $danaSet->created_at,
                'updated_at' => $danaSet->updated_at,
                'insert_type' => 1,
            ]);
            return $response['key'];
        }
        Log::channel('danaTransfer')->debug("In DanaService : request to dana API for creating course {$set->id} was not successful , response status : ".$response['status_code']);
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
        if ($response['status_code'] == 200 or $response['status_code'] == 400) {
            $option = [
                'headers' => [
                    'Authorization' => DanaService::danaToken(),
                ],
                'query' => [
                    'key' => $path.$name,
                ],
            ];
            $response = self::send('GET', '/CloudFile/getId', $option);
            if ($response['status_code'] != 200) {
                if ($type == 'image') {
                    return 27265;
                } //TODO: notfound image ,it work now with this id
                else {
                    throw new Exception('video does not upload, videoName='.$name, 500);
                }
            }

            return $response['result'];
        }

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

    public static function danaToken()
    {
        $danaToken = DanaToken::all()->first();
        return 'Bearer '.$danaToken?->access_token;
    }

    public static function send($method, $url, $option)
    {
        try {
            $client = new Client();
            $response = $client->request(
                $method,
                'https://ugcbe.danaapp.ir/api'.$url,
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

    public static function getAuthorId($id)
    {
        return DanaAuthor::where('author_id', $id)->first()?->dana_author_id;
    }

    public static function createVideoSession($courseId, $content, $order)
    {
        $danaSession = DanaContentTransfer::query()->where('educationalcontent_id', $content->id)->first();
        if (!isset($danaSession)) {
            $danaSession = DanaContentTransfer::create(
                [
                    'educationalcontent_id' => $content->id,
                    'dana_course_id' => $courseId,
                    'dana_session_id' => null,
                    'dana_content_id' => null,
                    'dana_filemanager_content_id' => null,
                    'status' => DanaContentTransfer::NOT_TRANSFERRED,
                ]);
        }

        /**
         * Upload videos
         */
        $files = json_decode($content->getAttributes()['file']);
        $videoId = null;
        foreach ($files as $file) {
            if ($file->res != '720p') {
                continue;
            }
            $videoUrl = config('filesystems.disks.productFileSFTP.download_endpoint').$file->fileName;
            $videoId = self::uploadFile($videoUrl, substr($file->fileName, strrpos($file->fileName, '/') + 1));
        }
        if (is_null($videoId)) {
            $danaSession->update([
                'status' => DanaContentTransfer::FAILED_TRANSFER,
            ]);
//            Log::channel('danaTransfer')->debug('In DanaService : ' . '720p quality was not uploaded contentId' . $content->id);
            throw new Exception('720p quality not found for contentId '.$content->id, 500);
        }

        $sessionName = $content->order == 0 ? $content->name : $content->displayName;
        /**
         * AddCourseSession
         */
        $body = [
            'CourseId' => $courseId,
            'SessionsCount' => $order,
            'Name' => $sessionName,
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
            $danaSession->update([
                'status' => DanaContentTransfer::FAILED_TRANSFER,
            ]);
//            Log::channel('danaTransfer')->debug("In DanaService : request to dana API for AddCourseSession {$content->id} was not successful , response status : " . $response['status_code']);
            throw new Exception('failed to create session for contentId'.$content->id, 500);
        }
        $danaSessionId = self::getDanaSessionId($courseId, $sessionName);
        if (is_null($danaSessionId)) {
//            Log::channel('danaTransfer')->debug("In DanaService : request to dana API for getting session id of {$content->id} was not successful , response status : " . $response['status_code']);
            return false;
        }
        $danaSession->update([
            'dana_session_id' => $danaSessionId,
            'status' => DanaContentTransfer::TRANSFERRING,
        ]);

        /**
         * AddSessionContent
         */
        $body = [
            'courseId' => $courseId,
            'sessionId' => $danaSessionId,
            'type' => 1,
            'isFree' => false,
            'title' => 'فیلم',
            'isDownloadable' => false,
            'contentId' => $videoId,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/AddSessionContent', $option);
        if ($response['status_code'] != 200) {
//            Log::channel('danaTransfer')->debug("In DanaService : request to dana API for creating content {$content->id} was not successful , response status : " . $response['status_code']);
            return false;
        }

        $danaSession->update([
            'dana_filemanager_content_id' => $videoId,
            'dana_content_id' => self::getDanaSessionContentId($courseId, $danaSessionId, 'فیلم'),
            'status' => DanaContentTransfer::SUCCESSFULLY_TRANSFERRED,
        ]);

        $pamphlets = Content::query()->where('contentset_id', $content->contentset_id)
            ->where('contenttype_id', config('constants.CONTENT_TYPE_PAMPHLET'))
            ->active()
            ->whereNull('redirectUrl')
            ->where('order', $content->order)
            ->get();

        foreach ($pamphlets as $pamphlet) {
            self::createPamphletOfSession($courseId, $pamphlet, $danaSessionId);
        }
        return $response['key'];
    }

    public static function getDanaSessionId($courseId, $name)
    {
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
            ],
        ];
        $response = self::send('GET', '/Course/GetCourseSessionInfo/'.$courseId, $option);
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['name'] == $name) {
                return $row['sessionID'];
            }
        }
    }

    public static function getDanaSessionContentId($courseId, $sessionId, $name)
    {
        $response = self::getDanaSessionContent($courseId, $sessionId);
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['name'] == $name) {
                return $row['id'];
            }
        }
    }

    public static function getDanaSessionContent($courseId, $sessionId)
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

    public static function createPamphletOfSession($courseId, $pamphlet, $danaSessionId)
    {
        /**
         * Upload videos
         */
        $file = json_decode($pamphlet->getAttributes()['file']);
        $pamphletUrl = config('filesystems.disks.productFileSFTP.download_endpoint').$file[0]->fileName;
        $pamphletId = self::uploadFile($pamphletUrl, basename($pamphletUrl));
        if (is_null($pamphletId)) {
//            Log::channel('danaTransfer')->debug('In DanaService : ' . 'pamphlet was not uploaded , contentId' . $pamphlet->id);
            throw new Exception('pdf not found for contentId'.$pamphlet->id, 500);
        }
        /**
         * AddSessionContent
         */
        $body = [
            'courseId' => $courseId,
            'sessionId' => $danaSessionId,
            'type' => 3,
            'isFree' => false,
            'title' => 'جزوه',
            'isDownloadable' => false,
            'contentId' => $pamphletId,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/AddSessionContent', $option);
        if ($response['status_code'] != 200) {
//            Log::channel('danaTransfer')->debug("In DanaService : request to dana API for creating pamphlet {$pamphlet->id} was not successful , response status : " . $response['status_code']);
            return false;
        }
        DanaContentTransfer::create(
            [
                'dana_course_id' => $courseId,
                'dana_filemanager_content_id' => $pamphletId,
                'educationalcontent_id' => $pamphlet->id,
                'dana_session_id' => $danaSessionId,
                'dana_content_id' => self::getDanaSessionContentId($courseId, $danaSessionId, 'جزوه'),
                'status' => DanaContentTransfer::SUCCESSFULLY_TRANSFERRED,
            ]);
        return true;
    }

    public static function createContent($courseId, $sessionId, $content)
    {
        $danaSession = DanaContentTransfer::query()->where('educationalcontent_id', $content->id)->first();
        if (!isset($danaSession)) {
            $danaSession = DanaContentTransfer::create(
                [
                    'educationalcontent_id' => $content->id,
                    'dana_course_id' => $courseId,
                    'dana_session_id' => $sessionId,
                    'dana_content_id' => null,
                    'dana_filemanager_content_id' => null,
                    'status' => DanaContentTransfer::NOT_TRANSFERRED,
                ]);
        }


        if ($content->contenttype_id == config('constants.CONTENT_TYPE_VIDEO')) {
            $danaContentType = 1;
            $danaContentName = 'فیلم';
            /**
             * Upload videos
             */
            $files = json_decode($content->getAttributes()['file']);
            $filemanagerId = null;
            foreach ($files as $file) {
                if ($file->res != '720p') {
                    continue;
                }
                $videoUrl = config('filesystems.disks.productFileSFTP.download_endpoint').$file->fileName;
                $filemanagerId = self::uploadFile($videoUrl,
                    substr($file->fileName, strrpos($file->fileName, '/') + 1));
            }
            if (is_null($filemanagerId)) {
                $danaSession->update([
                    'status' => DanaContentTransfer::FAILED_TRANSFER,
                ]);
                Log::channel('debug')->debug('In DanaService : '.'720p quality was not uploaded contentId'.$content->id);
            }
        } else {
            $danaContentType = 3;
            $danaContentName = 'جزوه';
            /**
             * Upload pamphlet
             */
            $file = json_decode($content->getAttributes()['file']);
            $pamphletUrl = config('filesystems.disks.productFileSFTP.download_endpoint').$file[0]->fileName;
            $filemanagerId = self::uploadFile($pamphletUrl, basename($pamphletUrl));
            if (is_null($filemanagerId)) {
                Log::channel('debug')->debug('In DanaService : '.'pamphlet was not uploaded , contentId'.$content->id);
            }
        }
        /**
         * AddSessionContent
         */
        $body = [
            'courseId' => $courseId,
            'sessionId' => $sessionId,
            'type' => $danaContentType,
            'isFree' => false,
            'title' => $danaContentName,
            'isDownloadable' => false,
            'contentId' => $filemanagerId,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/AddSessionContent', $option);
        if ($response['status_code'] != 200) {
//            Log::channel('debug')->debug("In DanaService : request to dana API for creating content {$content->id} was not successful , response status : " . $response['status_code']);
            return false;
        }

        $danaSession->update([
            'dana_filemanager_content_id' => $filemanagerId,
            'dana_content_id' => self::getDanaSessionContentId($courseId, $sessionId, $danaContentName),
            'status' => DanaContentTransfer::SUCCESSFULLY_TRANSFERRED,
        ]);
        return true;
    }

    public static function createPamphletSession($courseId, $content, $order)
    {
        $danaSession = DanaContentTransfer::query()->where('educationalcontent_id', $content->id)->first();
        if (!isset($danaSession)) {
            $danaSession = DanaContentTransfer::create(
                [
                    'educationalcontent_id' => $content->id,
                    'dana_course_id' => $courseId,
                    'dana_session_id' => null,
                    'dana_content_id' => null,
                    'dana_filemanager_content_id' => null,
                    'status' => DanaContentTransfer::NOT_TRANSFERRED,
                ]);
        }

        /**
         * Upload videos
         */
        $file = json_decode($content->getAttributes()['file']);
        $pamphletUrl = config('filesystems.disks.productFileSFTP.download_endpoint').$file[0]->fileName;
        $pamphletId = self::uploadFile($pamphletUrl, basename($pamphletUrl));
        if (is_null($pamphletId)) {
//            Log::channel('danaTransfer')->debug('In DanaService : ' . 'pamphlet was not uploaded , contentId' . $content->id);
            throw new Exception('pdf not found for contentId'.$content->id, 500);
        }

        $sessionName = $content->name;
        /**
         * AddCourseSession
         */
        $body = [
            'CourseId' => $courseId,
            'SessionsCount' => $order,
            'Name' => $sessionName,
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
            $danaSession->update([
                'status' => DanaContentTransfer::FAILED_TRANSFER,
            ]);
//            Log::channel('danaTransfer')->debug("In DanaService : request to dana API for AddCourseSession {$content->id} was not successful , response status : " . $response['status_code']);
            throw new Exception('failed to create session for contentId'.$content->id, 500);
        }
        $danaSessionId = self::getDanaSessionId($courseId, $sessionName);
        if (is_null($danaSessionId)) {
//            Log::channel('danaTransfer')->debug("In DanaService : request to dana API for getting session id of {$content->id} was not successful , response status : " . $response['status_code']);
            return false;
        }
        $danaSession->update([
            'dana_session_id' => $danaSessionId,
            'status' => DanaContentTransfer::TRANSFERRING,
        ]);

        /**
         * AddSessionContent
         */
        $body = [
            'courseId' => $courseId,
            'sessionId' => $danaSessionId,
            'type' => 3,
            'isFree' => false,
            'title' => 'جزوه',
            'isDownloadable' => false,
            'contentId' => $pamphletId,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];
        $response = self::send('POST', '/Course/AddSessionContent', $option);
        if ($response['status_code'] != 200) {
//            Log::channel('danaTransfer')->debug("In DanaService : request to dana API for creating content {$content->id} was not successful , response status : " . $response['status_code']);
            return false;
        }

        $danaSession->update([
            'dana_filemanager_content_id' => $pamphletId,
            'dana_content_id' => self::getDanaSessionContentId($courseId, $danaSessionId, 'جزوه'),
            'status' => DanaContentTransfer::SUCCESSFULLY_TRANSFERRED,
        ]);
        return $response['key'];
    }

    public static function getDanaSessionContentIdByFileManagerId($courseId, $sessionId, $filemanagerId)
    {
        $response = self::getDanaSessionContent($courseId, $sessionId);
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['contentId'] == $filemanagerId) {
                return $row['id'];
            }
        }
    }

    public static function createTeacher($teacher)
    {
        $teacherDto = [
            'cellPhone' => $teacher->mobile,
            'email' => $teacher->mobile.'@alaatv.com',
            'family' => $teacher->lastName,
            'name' => $teacher->firstName,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => '{
              "teacherDto": '.json_encode($teacherDto).',
            }',
        ];

        $response = self::send('POST', '/User/CreateTeacher', $option);
        if ($response['status_code'] == Response::HTTP_OK) {
            return true;
        }
//        Log::channel('danaTransfer')->debug('DanaService : createTeacher : ' . $response['status_code']);
        return false;
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
            Log::channel('danaTransfer')->debug('In DanaService : session was not updated : status is '.$response['status_code']);
            Log::channel('danaTransfer')->debug(json_encode($priorityData));
        }

        if (isset($response['isSuccess']) && !$response['isSuccess']) {
            Log::channel('danaTransfer')->debug('In DanaService : session was not updated : isSuccess is false');
            Log::channel('danaTransfer')->debug(json_encode($priorityData));
        }
    }

    public static function getTeachers()
    {
        $teacherDto = [
            'current' => 1,
            'pageSize' => 100,
        ];
        $option = [
            'headers' => [
                'Authorization' => DanaService::danaToken(),
                'Content-Type' => 'application/json',
            ],
            'body' => '{
              "dtParameter": '.json_encode($teacherDto).',
              "isPaginate": false
            }',
        ];

        return self::send2('POST', '/User/GetTeachers', $option);
    }

    public static function send2($method, $url, $option)
    {
        try {
            $client = new Client();
            $response = $client->request(
                $method,
                'https://ugcbe.danaapp.ir/api'.$url,
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
}
