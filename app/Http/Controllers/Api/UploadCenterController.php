<?php

namespace App\Http\Controllers\Api;

use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\PresignedRequest;
use App\Models\Content;
use Illuminate\Support\Facades\Storage;
use function response;

class UploadCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.PUT_MINIO_UPLOAD'), ['only' => 'presignedRequest', 'upload']);
        $this->middleware('throttle:'.config('constants.PRESIGNED_REQUEST_ATTEMPTS_MINIO'),
            ['only' => 'presignedRequest']);
    }

    public function presignedRequest(PresignedRequest $request)
    {
        $minio = Storage::disk(config('constants.ALAA_DISK_NAME'));
        $client = $minio->getDriver()->getAdapter()->getClient();
        $expiry = '+'.config('constants.PRESIGNED_REQUEST_EXPIRY_MINIO').' minutes';
        $command = $client->getCommand('PutObject', [
            'Bucket' => env('AWS_ALAA_BUCKET'),
            'Key' => $request->get('key')
        ]);
        $request = $client->createPresignedRequest($command, $expiry);
        $content = Content::create([
            'content_status_id' => config('constants.CONTENT_STATUS_PENDING')
        ]);
        return response()->json(['data' => ['url' => (string) $request->getUri(), 'content_id' => $content->id]]);
    }

    public function upload(PresignedRequest $request)
    {
        return response()->json([
            'data' => [
                'url' => Uploader::preSignedUrl(
                    $request,
                    env('AWS_PUBLIC_BUCKET'),
                    config('constants.PRESIGNED_REQUEST_EXPIRY_MINIO'),
                    'alaaPages/'.Uploader::makeFolderName().'/',
                )
            ]
        ]);
    }
}
