<?php


namespace App\Classes\Uploader;


use App\Classes\Uploader\Drives\U_3S;
use Illuminate\Http\Request;

/**
 * @method static put($file, $disk, $model = null, $fileName = null)
 * @method static url($disk, $fileName, $removeFileNamePrefix = true)
 * @method static delete($disk, $fileName, $removeFileNamePrefix = true)
 * @method static size($disk, $fileName, $model = null)
 * @method static update($disk, $model, $lastPath, $newFile)
 * @method static get($disk, $fileName)
 * @method static mimeType($disk, $fileName)
 * @method static readStream($disk, $fileName)
 * @method static privateUrl($disk, $validityDuration, $model = null, $fileName = null)
 * @method static makeFolderName()
 * @method static preSignedUrl(Request $request, string $bucket, string $expiry, string $filePath)
 */
class Uploader
{

    use UploaderTrait;

    public static $drive = 'U_3S';

    public static function __callStatic($name, $arguments)
    {
        if (static::$drive === 'U_3S') {
            return U_3S::{$name}(...$arguments);
        }

        return null;
    }

}
