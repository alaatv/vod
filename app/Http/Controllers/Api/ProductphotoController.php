<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsertProductPhotoRequest;
use App\Models\Product;
use App\Models\Productphoto;
use App\Traits\FileCommon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class ProductphotoController extends Controller
{
    use FileCommon;

    public function __construct()
    {

    }

    public function store(InsertProductPhotoRequest $request)
    {
        $response = [];

        $productId = $request->get('product_id');
        $photo = new Productphoto();
        $photo->fill($request->validated());
        if ($request->get('enable') != 1) {
            $photo->enable = 0;
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = basename($file->getClientOriginalName(), '.'.$extension).'_'.date('YmdHis').'.'.$extension;
            $photo->setPhoto($file, config('disks.PRODUCT_IMAGE_MINIO'), $fileName);
        }

        if ($photo->save()) {
            Cache::tags([
                'product_'.$productId.'_samplePhotos'
            ])->flush();
            $response['success'] = 'درج عکس با موفقیت انجام شد';
        } else {
            $response['error'] = 'خطای پایگاه داده';
        }

        return Response::json($response);
    }

    public function destroy(Productphoto $productphoto)
    {
        try {
            $productphoto->delete();
        } catch (Exception $e) {
            return Response::json(['error' => 'عملیات حذف نمونه عکس محصول با خطا مواجه شد!']);
        }
        return Response::json(['success' => 'حذف نمونه عکس محصول با موفقیت انجام شد.']);
    }

    public function updateOrder(Request $request, Product $product)
    {
        try {
            foreach ($request->product_photo_orders as $photoId => $order) {
                $product->photos()->where('id', $photoId)->update(['order' => $order]);
            }
        } catch (Exception $exception) {
            return Response::json(['error' => 'عملیات به روز رسانی ترتیب نمونه عکس های محصولات با خطا مواجه شده است!']);
        }
        return Response::json(['success' => 'به روز رسانی ترتیب نمونه عکس های محصولات با موفقیت انجام شد.']);
    }
}