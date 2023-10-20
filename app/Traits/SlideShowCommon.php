<?php namespace App\Traits;

use App\Classes\Uploader\Uploader;
use App\Models\Slideshow;
use Illuminate\Http\UploadedFile;

trait SlideShowCommon
{
    public function storeImageOfSlideShow(UploadedFile $file, Slideshow $slideshow)
    {
        $fileName = Uploader::put($file, config('disks.HOME_SLIDESHOW_PIC_MINIO'), $slideshow);
        if (isset($fileName)) {
            $slideshow->photo = $fileName;
        }
        if (!isset($fileName)) {
            session()->put('error', 'بارگذاری عکس بسته با مشکل مواجه شد!');
        }
    }
}
