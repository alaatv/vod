<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateRoleRequest;
use App\Http\Requests\Admin\EditRoleRequest;
use App\Http\Requests\Admin\SyncRoleRequest;
use App\Http\Resources\Role as RoleResource;
use App\Models\Role;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{


    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return RoleResource::collection(Role::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRoleRequest  $request
     * @return JsonResponse
     */
    public function store(CreateRoleRequest $request)
    {
        try {
            $role = Role::create($request->validated());
            $role->attachPermissions($request->get('permissions', []));
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(new RoleResource($role->load('permissions')), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Role  $role
     * @return JsonResponse
     */
    public function show(Role $role)
    {
        return response()->json(new RoleResource($role->load('permissions')), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditRoleRequest  $request
     * @param  Role  $role
     * @return JsonResponse
     */
    public function update(EditRoleRequest $request, Role $role)
    {
        try {
            $role->update($request->validated());
            $role->permissions()->sync($request->get('permissions', []));
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(new RoleResource($role->load('permissions')), Response::HTTP_OK);
    }

    /**
     * sync many permissions to role
     *
     * @param  SyncRoleRequest  $request
     * @param  Role  $role
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function sync(SyncRoleRequest $request, Role $role)
    {
        //todo cache
        $permissions = $request->get('permissions', []);

        $permissions = collect($permissions)->filter();

        $role->permissions()->sync($permissions);

        return new RoleResource($role->load('permissions'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role  $role
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Role $role)
    {
        if ($role->isDefault) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'Role can not be destroyed!');
        }

        try {
            $role->delete();
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

}

