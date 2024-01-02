<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\UserSearchOnlyTrashed;
use App\Classes\Search\UserSearchWithoutTrashed;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserApiRequest;
use App\Http\Requests\Admin\EditUserApiRequest;
use App\Http\Requests\Admin\SyncPermissionUserApiRequest;
use App\Http\Requests\Admin\SyncRoleUserApiRequest;
use App\Http\Resources\Permission;
use App\Http\Resources\Role;
use App\Models\User;
use App\Traits\SearchCommon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use SearchCommon;

    public function __construct()
    {
        //        $this->middleware('permission:'.config('constants.LIST_USER_ACCESS'), ['only' => 'index']);
        //        $this->middleware('permission:'.config('constants.INSERT_USER_ACCESS'), ['only' => 'store']);
        //        $this->middleware('permission:'.config('constants.SHOW_USER_ACCESS'), ['only' => 'show']);
        //        $this->middleware('permission:'.config('constants.EDIT_USER_ACCESS'), ['only' => 'update']);
        //        $this->middleware('permission:'.config('constants.REMOVE_USER_ACCESS'), ['only' => 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request, UserSearchWithoutTrashed $userSearch)
    {
        if ($this->validateLengthPaginate($request->input('length'))) {
            $userSearch->setNumberOfItemInEachPage($request->input('length'));
        }
        $mapDetails = $userSearch->get($request->all());

        return \App\Http\Resources\User::collection($mapDetails);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(CreateUserApiRequest $request)
    {
        $validated = $request->validated();
        if (! $request->input('password')) {
            $validated['password'] = bcrypt($request->input('nationalCode'));
        } else {
            $validated['password'] = bcrypt($request->input('password'));
        }

        try {
            $user = User::create($validated);
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(new \App\Http\Resources\User($user->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show(User $user)
    {

        return response()->json(new \App\Http\Resources\User($user), Response::HTTP_OK);
    }

    /**
     * Display list of soft delete users
     *
     * @return JsonResponse
     */
    public function trashedListUsers(Request $request, UserSearchOnlyTrashed $userSearch)
    {
        if ($this->validateLengthPaginate($request->input('length'))) {

            $userSearch->setNumberOfItemInEachPage($request->input('length'));

        }

        $mapDetails = $userSearch->get($request->all());

        return \App\Http\Resources\User::collection($mapDetails)->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return JsonResponse
     */
    public function update(EditUserApiRequest $request, User $user)
    {
        $validated = $request->validated();
        // todo cache
        if ($request->input('password')) {

            $validated['password'] = bcrypt($request->input('password'));

        }

        try {
            $user->update($validated);
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(new \App\Http\Resources\User($user), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    //    /**
    //     * restore user
    //     *
    //     * @param RestoreUserApiRequest $request
    //     * @return JsonResponse
    //     */
    //    public function restore(RestoreUserApiRequest $request)
    //    {
    //        //ToDo restore all relations for this model
    //        // todo cache
    //        $user = User::withTrashed()->firstWhere('id', $request->input('user_id'));
    //        $user->restore();
    //
    //        return new \App\Http\Resources\User($user);
    //    }

    /**
     * show roles a user
     *
     * @return JsonResponse
     */
    public function syncRoles(SyncRoleUserApiRequest $request, User $user)
    {
        // todo cache
        $roles = $request->get('roles', []);

        $roles = collect($roles)->filter();

        $user->roles()->sync($roles);

        return Role::collection($user->roles);
    }

    /**
     * show roles a user
     *
     * @return JsonResponse
     */
    public function roles(User $user)
    {
        // todo cache
        return Role::collection($user->roles);
    }

    /**
     * show permission a user
     *
     * @return JsonResponse
     */
    public function permission(User $user)
    {
        // todo cache
        return Permission::collection($user->permissions);
    }

    /**
     * show permission a user
     *
     * @return JsonResponse
     */
    public function syncPermission(SyncPermissionUserApiRequest $request, User $user)
    {
        // todo cache
        $permissions = $request->get('permissions', []);

        $permissions = collect($permissions)->filter();

        $user->permissions()->sync($permissions);

        return Permission::collection($user->permissions);
    }
}
