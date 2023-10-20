<?php namespace App\Traits;

use App\Classes\Uploader\Uploader;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait FileCommon
{
    /** Obtains file size based on it's url
     *
     * @param  string  $url
     *
     * @return string
     */
    public function curlGetFileSize($url): string
    {
        $curl = curl_init($url);

        // Issue a HEAD request and follow any redirects.
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        if (request()->server('HTTP_USER_AGENT') !== null) {
            curl_setopt($curl, CURLOPT_USERAGENT, request()->server('HTTP_USER_AGENT'));
        }
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);

        curl_exec($curl);
        $fileSizeString = '';
        if (curl_errno($curl)) {
            curl_close($curl);

            return $fileSizeString;
        }
        switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
            case 200:
                $fileSize = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                $fileSizeString = $this->FileSizeConvert($fileSize);
                break;
            default:
                $fileSizeString = '';
        }

        curl_close($curl);

        return $fileSizeString;
    }

    /** Converts file size to Persian string
     *
     * @param $bytes
     *
     * @return string
     */
    public function FileSizeConvert($bytes): string
    {
        $result = '';
        $bytes = floatval($bytes);
        $arBytes = [
            0 => [
                'UNIT' => 'ترابایت',
                'VALUE' => pow(1024, 4),
            ],
            1 => [
                'UNIT' => 'گیگابایت',
                'VALUE' => pow(1024, 3),
            ],
            2 => [
                'UNIT' => 'مگابایت',
                'VALUE' => pow(1024, 2),
            ],
            3 => [
                'UNIT' => 'کیلوبایت',
                'VALUE' => 1024,
            ],
            4 => [
                'UNIT' => 'بایت',
                'VALUE' => 1,
            ],
        ];

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem['VALUE']) {
                $result = $bytes / $arItem['VALUE'];
                $result = str_replace('.', '.', strval(round($result, 2))).' '.$arItem['UNIT'];
                break;
            }
        }

        return $result;
    }

    /**
     * @param        $file
     *
     * @param  string  $disk
     *
     * @return string|null
     * @throws FileNotFoundException
     */
    private function storePhoto($file, string $disk): ?string
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = basename($file->getClientOriginalName(), '.'.$extension).'_'.date('YmdHis').'.'.$extension;
        $fullPath = config('constants.MINIO_UPLOAD_PATH_SOURCE');
        $storeProcess = $this->storeFileMinio($file, config('constants.MINIO_UPLOAD_DEFAULT_BUCKET'), $fullPath);
        if (isset($storeProcess)) {
            return substr($fullPath, 1).$fileName;
        } else {
            return null;
        }
    }

    private function storePhotoMinio($file, string $disk, string $path = ''): ?string
    {
        return Uploader::put($file, $disk) ?? null;

    }

    /**
     * @param        $file
     *
     * @param  string  $disk
     *
     * @return string|null
     * @throws FileNotFoundException
     */
    private function storeFileToLocalCdn($file, string $disk): ?string
    {


        $extension = $file->getClientOriginalExtension();

        $fileName =
            basename($file->getClientOriginalName(), '.'.$extension).'_'.date('YmdHis').'.'.$extension;
        $disk = Storage::disk($disk);

        if ($disk->put($fileName, File::get($file))) {
            $fullPath = $disk->getAdapter()->getpathPrefix();
            $photo = $this->getSubDirectoryInCDN($fullPath).$fileName;
        }
        return (isset($photo)) ? $photo : null;
    }

    /**
     * @param  string  $fullPath
     *
     * @return string
     */
    protected function getSubDirectoryInCDN(string $fullPath): string
    {
        $baseRoot = Storage::Disk(config('disks.ALAA_CDN_SFTP'))->getAdapter()->getRoot();
        return explode($baseRoot, $fullPath)[1];
    }
}
