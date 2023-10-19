<?php


namespace App\Classes\Uploader;


use App\Classes\Uploader\Drives\U_3S;
use App\Traits\MinioPathMaker;
use Illuminate\Http\UploadedFile;

trait UploaderTrait
{
    use MinioPathMaker;

    public static function fileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();

        return time().'_'.random_int(1000, 9999).'.'.$extension;
    }

    protected static function getBucket(string $disk): ?string
    {
        return MinioPathMaker::getBucket($disk);
    }

    protected static function getDownloadEndpoint(string $disk): ?string
    {
        return MinioPathMaker::getDownloadEndpoint($disk);
    }

    protected static function getDownloadUrl(string $disk, string $url): ?string
    {
        return MinioPathMaker::getDownloadUrl($disk, $url);
    }

    protected static function createFilePath(string $fileName, string $disk, bool $removeFileNamePrefix = true): string
    {
        $fileName = $removeFileNamePrefix ? U_3S::getLastPart($fileName) : $fileName;
        $path = self::makePath($disk);
        return $path.$fileName;
    }

    protected static function makePath(string $disk): ?string
    {
        return MinioPathMaker::makePath($disk);
    }
}
