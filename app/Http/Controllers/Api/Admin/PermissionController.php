<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreatePermissionRequest;
use App\Http\Requests\Admin\EditPermissionRequest;
use App\Models\Permission;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_PERMISSION_ACCESS'), ['only' => 'index']);
        $this->middleware('permission:'.config('constants.INSERT_PERMISSION_ACCESS'), ['only' => 'store']);
        $this->middleware('permission:'.config('constants.SHOW_PERMISSION_ACCESS'), ['only' => 'show']);
        $this->middleware('permission:'.config('constants.EDIT_PERMISSION_ACCESS'), ['only' => 'update']);
        $this->middleware('permission:'.config('constants.REMOVE_PERMISSION_ACCESS'), ['only' => 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return \App\Http\Resources\Permission::collection(Permission::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreatePermissionRequest  $request
     * @return JsonResponse
     */
    public function store(CreatePermissionRequest $request)
    {
        try {
            $permission = Permission::create($request->validated());
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(new \App\Http\Resources\Permission($permission->fresh()),
            Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Permission  $permission
     * @return JsonResponse
     */
    public function show(Permission $permission)
    {
        return response()->json(new \App\Http\Resources\Permission($permission), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditPermissionRequest  $request
     * @param  Permission  $permission
     * @return JsonResponse
     */
    public function update(EditPermissionRequest $request, Permission $permission)
    {
        try {
            $permission->update($request->validated());
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(new \App\Http\Resources\Permission($permission), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Permission  $permission
     * @return JsonResponse
     */
    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
