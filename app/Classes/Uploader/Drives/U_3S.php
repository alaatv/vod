<?php

namespace App\Classes\Uploader\Drives;

use App\Classes\Uploader\UploaderTrait;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class U_3S
{
    use UploaderTrait;

    public static function url(string $disk, string $fileName, bool $removeFileNamePrefix = true): ?string
    {
        $filename = $removeFileNamePrefix ? self::getLastPart($fileName) : $fileName;
        $path = self::makePath($disk);
        $bucket = self::getBucket($disk);
        $downloadEndpoint = self::getDownloadEndpoint($disk);

        return "$downloadEndpoint/$bucket/$path$filename";
    }

    public static function getLastPart(?string $path = null, string $delimiter = '/'): string
    {
        return empty($path) ? '' : Arr::last(explode($delimiter, $path));
    }

    public static function size(string $disk, string $fileName, Model $model = null): int
    {
        $fileName = self::createFilePath($fileName, $disk);
        try {
            return Storage::disk($disk)->size($fileName);
        } catch (Exception $e) {
            return 0;
        }
    }

    public static function delete(string $disk, string $fileName, bool $removeFileNamePrefix = true): bool
    {
        $fileName = self::createFilePath($fileName, $disk, $removeFileNamePrefix);
        return Storage::disk($disk)->delete($fileName);
    }

    public static function update(string $disk, Model $model, ?string $lastPath, UploadedFile $newFile)
    {
        if ($lastPath == null) {
            return self::put($newFile, $disk, $model);
        }
        $fileName = self::createFilePath($lastPath, $disk);
        return Storage::disk($disk)->update($fileName, $newFile->get());
    }

    /**
     * @param  UploadedFile|string  $file
     * @param  string  $disk
     * @param  Model|null  $model
     * @param  string|null  $fileName
     * @param  bool  $nonUploaded
     * @return string|null
     * @throws FileNotFoundException
     */
    public static function put(
        $file,
        string $disk,
        Model $model = null,
        string $fileName = null,
        bool $nonUploaded = false
    ): ?string {
        try {
            $path = self::makePath($disk);
            $fileName = $fileName ?? static::fileName($file);
            try {
                Storage::disk($disk)->put($path.$fileName, file_get_contents($file));
            } catch (S3Exception $exception) {
                Log::info('U_3S put method: error : '.$exception->getMessage());
                if ($exception->getStatusCode() !== 415) {
                    throw $exception;
                }

                return null;
                // TODO: Exception handling refactor
            } catch (Error $error) {
                Log::info('U_3S put method: error : '.$error->getMessage());
                return null;
            } catch (Exception $exception) {
                Log::info('U_3S put method: exception: '.$exception->getMessage());
                return null;
            } catch (Throwable $throwable) {
                Log::info('U_3S put method: throwable: '.$throwable->getMessage());
                return null;
            }
            return Storage::cloud()->path($fileName);
        } catch (Exception $exception) {
            Log::info('U_3S put method: exception: '.$exception->getMessage());
            return null;
        }
    }

    public static function get($disk, $fileName)
    {
        $fileName = self::createFilePath($fileName, $disk);
        return Storage::disk($disk)->get($fileName);
    }

    public static function mimeType($disk, $fileName)
    {
        $fileName = self::createFilePath($fileName, $disk);
        return Storage::disk($disk)->getMimetype($fileName);
    }

    public static function readStream($disk, $fileName)
    {
        $fileName = self::createFilePath($fileName, $disk);
        return Storage::disk($disk)->readStream($fileName);
    }

    public static function privateUrl(
        string $disk,
        string $secondsOfValidity,
        Model $model = null,
        string $fileName = null
    ): ?string {
        if (!isset($fileName)) {
            return null;
        }

        $filename = Arr::last(explode('/', $fileName));
        $path = self::makePath($disk, $model);
        $adapter = Storage::disk($disk)->getDriver()->getAdapter();
        $url =
            Storage::disk($disk)->getAwsTemporaryUrl($adapter, $path.$fileName, now()->addSeconds($secondsOfValidity),
                []);

        return self::getDownloadUrl($disk, $url);
    }

    public static function makeFolderName()
    {
        return Carbon::now()->format('Y-m');
    }

    public static function preSignedUrl(Request $request, string $bucket, string $expiry, string $filePath): string
    {
        $client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => env('AWS_KEY'),
                'secret' => env('AWS_SECRET'),
            ]
        ]);
        $fileName = $request->query('key');
        $ext = substr($fileName, strpos($fileName, '.') + 1);
        $fileNameWithoutExtension = substr($fileName, 0, strpos($fileName, '.')).now()->timestamp;
        $cmd = $client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $filePath.$fileNameWithoutExtension.'.'.$ext,
        ]);
        $request = $client->createPresignedRequest($cmd, '+'.$expiry.'minutes');
        return (string) $request->getUri();
    }
}
