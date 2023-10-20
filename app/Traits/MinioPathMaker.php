<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait MinioPathMaker
{
    public static function makePath(string $disk): ?string
    {
        return config('filesystems.disks.'.$disk.'.path');
    }

    public static function getBucket(string $disk): ?string
    {
        return config('filesystems.disks.'.$disk.'.bucket');
    }

    public static function getDownloadUrl(string $disk, string $url): ?string
    {
        $endpoint = self::getEndpoint($disk);
        $downloadEndpoint = self::getDownloadEndpoint($disk);

        return str_replace($endpoint, $downloadEndpoint, $url);
    }

    public static function getEndpoint(string $disk): ?string
    {
        return config('filesystems.disks.'.$disk.'.endpoint');
    }

    public static function getDownloadEndpoint(string $disk): ?string
    {
        return config('filesystems.disks.'.$disk.'.download_endpoint');
    }

    private static function makeSuffix(Model $model): string
    {
        $type = Str::lower(Arr::last(explode('\\', get_class($model))));
        $suffix = match ($type) {
            '??' => self::contentSetPath($model),
            default => '',
        };

        return $suffix;
    }

    private static function contentSetPath(Model $model): string
    {
        return "{$model->id}/";
    }

    private static function productPath(Model $model): string
    {
        return "{$model->id}/";
    }

    private static function userPath(Model $model): string
    {
        return "{$model->id}/test/";
    }

}
