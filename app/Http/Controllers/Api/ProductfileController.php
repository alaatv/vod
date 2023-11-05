<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditProductfileRequest;
use App\Http\Requests\InsertProductfileRequest;
use App\Models\Product;
use App\Models\Productfile;
use App\Models\Productfiletype;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductfileController extends Controller
{

    public function __construct()
    {

        $this->middleware('permission:'.config('constants.LIST_PRODUCT_FILE_ACCESS'), ['only' => 'index']);
        $this->middleware('permission:'.config('constants.INSERT_PRODUCT_FILE_ACCESS'), [
            'only' => [
                'create',
                'store',
            ],
        ]);
        $this->middleware('permission:'.config('constants.REMOVE_PRODUCT_FILE_ACCESS'), ['only' => 'destroy']);
        $this->middleware('permission:'.config('constants.EDIT_PRODUCT_FILE_ACCESS'), [
            'only' => [
                'edit',
                'update',
            ],
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $productId = $request->get('product');
        $product = Product::FindOrFail($productId);
        $productFileTypes = Productfiletype::pluck('displayName', 'id')->toArray();
        $defaultProductFileOrders = collect();
        foreach ($productFileTypes as $key => $productFileType) {
            $lastProductFile = $product->productfiles->where('productfiletype_id', $key)
                ->sortByDesc('order')
                ->first();
            if (isset($lastProductFile)) {
                $lastOrderNumber = $lastProductFile->order + 1;
                $defaultProductFileOrders->push([
                    'fileTypeId' => $key,
                    'lastOrder' => $lastOrderNumber,
                ]);
            } else {
                $defaultProductFileOrders->push([
                    'fileTypeId' => $key,
                    'lastOrder' => 1,
                ]);
            }
        }
        $productFileTypes = Arr::add($productFileTypes, 0, 'انتخاب کنید');
        $productFileTypes = Arr::sortRecursive($productFileTypes);

        $products = $this->makeProductCollection();

        return new JsonResponse(compact('product', 'products', 'productFileTypes', 'defaultProductFileOrders'), 200);
    }

    public function store(InsertProductfileRequest $request): JsonResponse
    {
        $productFile = new Productfile();
        $productFile->fill($request->all());
        $time = $request->get('time');
        if (strlen($time) > 0) {
            $time = Carbon::parse($time)->format('H:i:s');
        } else {
            $time = '00:00:00';
        }
        $validSince = $request->get('validSinceDate');
        $validSince = Carbon::parse($validSince)->format('Y-m-d');
        $validSince = $validSince.' '.$time;
        $productFile->validSince = $validSince;

        if ($request->get('enable') != 1) {
            $productFile->enable = 0;
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = basename($file->getClientOriginalName(), '.'.$extension).'_'.date('YmdHis').'.'.$extension;
            if (Storage::disk(config('disks.PRODUCT_FILE'))->put($fileName, File::get($file))) {
                $productFile->file = $fileName;
            }
        }

        if ($request->has('cloudFile')) {
            $link = $request->get('cloudFile');
            $productFile->cloudFile = $link;
            if (!$request->hasFile('file')) {
                $fileName = basename($link);
                $productFile->file = $fileName;
            }
        }

        if ($productFile->productfiletype_id == 0) {
            $productFile->productfiletype_id = null;
        }

        if ($request->has('order') && isset($productFile->product->id)) {
            if (strlen(preg_replace('/\s+/', '', $request->get('order'))) == 0) {
                $productFile->order = 0;
            }
            $filesWithSameOrder = Productfile::all()
                ->where('product_id', $productFile->product->id)
                ->where('productfiletype_id', $productFile->productfiletype->id)
                ->where('order', $productFile->order);
            if (!$filesWithSameOrder->isEmpty()) {
                $filesWithGreaterOrder = Productfile::all()
                    ->where('productfiletype_id', $productFile->productfiletype->id)
                    ->where('order', '>=', $productFile->order);
                foreach ($filesWithGreaterOrder as $greaterProductFile) {
                    $greaterProductFile->order = $greaterProductFile->order + 1;
                    $greaterProductFile->update();
                }
            }
        }

        if ($productFile->save()) {
            return new JsonResponse(['message' => 'Product file added successfully'], 200);
        } else {
            return new JsonResponse(['message' => 'Database error'], 503);
        }
    }

    public function edit($productFile): JsonResponse
    {
        $validDate = Carbon::parse($productFile->validSince)->format('Y-m-d');
        $validTime = Carbon::parse($productFile->validSince)->format('H:i');
        $productFileTypes = Productfiletype::pluck('displayName', 'id')->toArray();
        $productFileTypes = Arr::add($productFileTypes, 0, 'انتخاب کنید');
        $productFileTypes = Arr::sortRecursive($productFileTypes);

        return new JsonResponse(compact('productFile', 'validDate', 'validTime', 'productFileTypes'), 200);
    }

    public function update(EditProductfileRequest $request, $productFile): JsonResponse
    {
        $oldFile = $productFile->file;
        $productFile->fill($request->all());

        $time = $request->get('time');
        if (strlen($time) > 0) {
            $time = Carbon::parse($time)->format('H:i:s');
        } else {
            $time = '00:00:00';
        }
        $validSince = $request->get('validSinceDate');
        $validSince = Carbon::parse($validSince)->format('Y-m-d');
        $validSince = $validSince.' '.$time;
        $productFile->validSince = $validSince;

        if ($request->get('enable') != 1) {
            $productFile->enable = 0;
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = basename($file->getClientOriginalName(), '.'.$extension).'_'.date('YmdHis').'.'.$extension;
            if (Storage::disk(config('disks.PRODUCT_FILE'))->put($fileName, File::get($file))) {
                Storage::disk(config('disks.PRODUCT_FILE'))->delete($oldFile);
                $productFile->file = $fileName;
            }
        }

        if ($request->has('cloudFile')) {
            $link = $request->get('cloudFile');
            $productFile->cloudFile = $link;
            if (!$request->hasFile('file')) {
                $fileName = basename($link);
                $productFile->file = $fileName;
            }
        }

        if ($productFile->productfiletype_id == 0) {
            $productFile->productfiletype_id = null;
        }

        if ($request->has('order') && isset($productFile->product->id)) {
            if (strlen(preg_replace('/\s+/', '', $request->get('order'))) == 0) {
                $productFile->order = 0;
            }
            $filesWithSameOrder = Productfile::all()
                ->where('id', '<>', $productFile->id)
                ->where('product_id', $productFile->product->id)
                ->where('productfiletype_id', $productFile->productfiletype->id)
                ->where('order', $productFile->order);
            if (!$filesWithSameOrder->isEmpty()) {
                $filesWithGreaterOrder = Productfile::all()
                    ->where('productfiletype_id', $productFile->productfiletype->id)
                    ->where('order', '>=', $productFile->order);
                foreach ($filesWithGreaterOrder as $greaterProductFile) {
                    $greaterProductFile->order = $greaterProductFile->order + 1;
                    $greaterProductFile->update();
                }
            }
        }

        if ($productFile->update()) {
            return new JsonResponse(['message' => 'درج فایل با موفقیت انجام شد'], 200);
        } else {
            return new JsonResponse(['message' => 'خطای پایگاه داده'], 503);
        }
    }
}