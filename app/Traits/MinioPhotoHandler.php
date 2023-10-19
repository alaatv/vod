<?php


namespace App\Traits;


use App\Classes\Uploader\Uploader;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

trait MinioPhotoHandler
{
    public function photoSize(string $disk)
    {
        return Uploader::size($disk, $this, $this->photo);
    }

    public function deletePhoto(string $disk)
    {
        Uploader::delete($disk, $this, $this->photo);
        $this->update([self::PHOTO_FIELD => null]);

    }

    public function updatePhoto(UploadedFile $file, string $disk): bool
    {
        try {
            $result = Uploader::update($disk, $this, $this->photo, $file);
            if (is_string($result)) {
                $this->update([
                    self::PHOTO_FIELD => $result,
                ]);
            }
            return true;
        } catch (Exception $exception) {
            Log::error("ERROR IN FILE UPLOAD FOR {$this?->id}.");
            return false;
        }

    }

    public function setPhoto(UploadedFile $file, string $disk, string $fileName = null)
    {
        $image = Uploader::put($file, $disk, $this, $fileName);
        $photoField = self::PHOTO_FIELD;
        $this->{$photoField} = $image;
        $this->update();
        return $image;
    }
}
