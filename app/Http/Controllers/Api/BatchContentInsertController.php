<?php

namespace App\Http\Controllers\Api;

use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\BatchContentInsertRequest;
use App\Models\BatchContentInsert;
use App\Models\Product;
use App\Services\BatchContentInsertService;

class BatchContentInsertController extends Controller
{
    public function __construct()
    {
        $this->callMiddlewares($this->getAuthExceptionArray());
    }

    /**
     * @param $authException
     */
    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
        $this->middleware('permission:'.config('constants.INSERT_BATCH_CONTENT'), ['only' => ['index', 'store'],]);
    }

    /**
     * @return array
     */
    private function getAuthExceptionArray(): array
    {
        return [];
    }

    public function index()
    {
        $products = Product::all();
        $inserts = BatchContentInsert::query()->orderByDesc('created_at')->paginate(10);
        return response()->json(compact('products', 'inserts'));
    }

    public function store(BatchContentInsertRequest $request, BatchContentInsertService $batchContentInsertService)
    {
        $file = $request->file('file');
        $fileName = str_replace(' ', '', $file->getClientOriginalName());
        if (Uploader::put($file, config('disks.GENERAL'), fileName: $fileName)) {
            $insert = auth()->user()->batchContentInserts()->create([
                'uploaded_file' => $fileName,
                'status' => 'processing',
            ]);
            $productId = $request->input('productId');
            $type = $request->input('type');
            $batchContentInsertService->setType($type)->setFileName($fileName)->setProductId($productId)->setInsertId($insert->id)->upload();
            return response()->json(['success' => 'درج با موفقیت انجام شد']);
        }
        return response()->json(['error' => 'آپلود فایل با خطا مواجه شد!']);
    }
}