<?php

namespace App\Traits;

use App\Classes\Uploader\Uploader;
use App\Models\File;
use App\Models\Productfile;
use App\Models\User;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Illuminate\Support\Facades\Storage;


trait ProductDownload
{

    public function makeExtraLinkK($productId, $fileName, $user = null)
    {
        if (!(isset($user) && !$user->isAbleTo(config('constants.DOWNLOAD_PRODUCT_FILE')))) {
            return null;
        }

        $products = ProductRepository::getProductsThatHaveValidProductFileByFileNameRecursively($fileName);

        $productId = array_merge($productId, $products->pluck('id')->toArray());

        return (new Productfile())->getExternalLinkForProductFileByFileName($fileName, $productId);
    }

    public function getProductFileErrors(?User $user, $fileName): ?string
    {
        if (!(isset($user) && !$user->isAbleTo(config('constants.DOWNLOAD_PRODUCT_FILE')))) {
            return null;
        }

        $products = ProductRepository::getProductsThatHaveValidProductFileByFileNameRecursively($fileName);
        if ($products->isEmpty()) {
            return 'چنین فایلی وجود ندارد ویا غیر فعال شده است';
        }

        $validOrders = $user?->getOrdersThatHaveSpecificProduct($products);
        if ($validOrders?->isEmpty()) {
            return $this->getMessageThatShouldByWhichProducts($products);
        }

        return null;
    }

    public function getFile($fileName)
    {
        $file = File::where('uuid', $fileName)->get();
        if ($file->isEmpty() || $file->count() > 1) {
            return false;
        }

        return $file;
    }

    public function getFileExternalLink($file)
    {
        $file = $file->first();
        if (!$file->disks->isNotEmpty()) {
            return $file->name;
        }
        return false;
    }

    public function getDiskName($contentType, $fileName)
    {
        switch ($contentType) {
            case 'عکس پروفایل':
                return config('disks.PROFILE_IMAGE');
            case 'عکس محصول':
                return config('disks.PRODUCT_IMAGE');
            case 'تمرین':
                // check if he has permission for downloading the assignment :

                //if(!Auth::user()->permissions->contains(Permission::all()->where("name", config('constants.DOWNLOAD_ASSIGNMENT_ACCESS'))->first()->id)) return redirect(action(("ErrorPageController@error403"))) ;
                //  checking permission through the user's role
                //$user->hasRole('goldenUser');
                return config('disks.ASSIGNMENT_QUESTION_FILE');
            case 'پاسخ تمرین':
                return config('disks.ASSIGNMENT_SOLUTION_FILE');
            case 'کاتالوگ محصول':
                return config('disks.PRODUCT_CATALOG_PDF');
            case 'سؤال مشاوره ای':
                return config('disks.CONSULTING_AUDIO_QUESTIONS');
            case 'تامبنیل مشاوره':
                return config('disks.CONSULTATION_THUMBNAIL');
            case 'عکس مقاله' :
                return config('disks.ARTICLE_IMAGE');
            case 'عکس اسلاید صفحه اصلی' :
                return config('disks.HOME_SLIDESHOW_PIC');
            case 'فایل سفارش' :
                return config('disks.ORDER_FILE_MINIO');
            case 'فایل کارنامه' :
                return config('disks.EVENT_REPORT');
            case 'exam' :
                if (Storage::disk(config('disks.EXAM_SFTP'))->exists($fileName)) {
                    return config('disks.EXAM_SFTP');
                } else {
                    return config('disks.EXAM');
                }
            case 'pamphlet':
                return config('disks.BOOK_SFTP');
            case 'book':
                if (Storage::disk(config('disks.BOOK_SFTP'))->exists($fileName)) {
                    return config('disks.BOOK_SFTP');
                } else {
                    return config('disks.BOOK');
                }
            case 'فایل محصول' :
                return config('disks.PRODUCT_FILE');
            default :
                return null;
        }
    }

    public function makeResponseFrom($diskType, $diskName, $fileName, $file = null)
    {
        switch ($diskType) {
            case 'SftpAdapter' :
                return $this->getFileFromSftp($file, $diskName, $fileName);
            case 'Local' :
                return $this->getFileFromLocal($diskName, $fileName);
            case 'AwsS3Adapter':
                return $this->getFileFromAws3($diskName, $fileName);
            default:
                abort(Response::HTTP_NOT_FOUND);
        }
    }

    public function getFileFromSftp($file, $diskName, $fileName)
    {
        try {

            $url = $file->getUrl();
            if (isset($url[0])) {
                return response()->redirectTo($url);
            }

            $fs = Storage::disk($diskName)->getDriver();
            $stream = $fs->readStream($fileName);

        } catch (Exception $exception) {
            Log::error('Fail getting file from Sftp', ['message' => $exception->getMessage()]);
            abort(Response::HTTP_NOT_FOUND);
        }

        return $this->makeStreamResponse($stream, $fs, $fileName);
    }

    public function makeStreamResponse($stream, $fs, $fileName)
    {
        return FacadesResponse::stream(function () use ($stream) {
            fpassthru($stream);
        }, Response::HTTP_OK, [
            'Content-Type' => $fs->getMimetype($fileName),
            'Content-Length' => $fs->getSize($fileName),
            'Content-disposition' => 'attachment; filename="'.basename($fileName).'"',
        ]);
    }

    public function getFileFromLocal($diskName, $fileName)
    {
        try {

            $fs = Storage::disk($diskName)->getDriver();
            $stream = $fs->readStream($fileName);

        } catch (Exception $exception) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return $this->makeStreamResponse($stream, $fs, $fileName);
    }

    public function getFileFromAws3($diskName, $fileName)
    {
        return FacadesResponse::stream(function () use ($diskName, $fileName) {
            $stream = Uploader::readStream($diskName, $fileName);
            fpassthru($stream);
        }, Response::HTTP_OK, [
            'Content-Type' => Uploader::mimeType($diskName, $fileName),
            'Content-Length' => Uploader::size($diskName, $fileName),
            'Content-disposition' => 'attachment; filename="'.basename($fileName).'"',
        ]);
    }

}
