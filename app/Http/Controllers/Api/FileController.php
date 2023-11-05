<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsertFileRequest;
use App\Models\File;

class FileController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertFileRequest  $request
     *
     * @return mixed
     */
    public function store(InsertFileRequest $request)
    {
        $file = new File();
        $file->fill($request->all());

        if ($file->save()) {
            if ($request->has('disk_id')) {
                $file->disks()->attach($request->get('disk_id'));
            }
            return $file->id;
        } else {
            return false;
        }
    }

    public function destroy(File $file)
    {
        if ($file->delete()) {
            session()->flash('success', 'فایل با موفقیت حذف شد');
            return response()->json(['message' => 'فایل با موفقیت اصلاح شد'], 200);
        } else {
            return response()->json(['message' => 'خطای پایگاه داده'], 500);
        }
    }
}