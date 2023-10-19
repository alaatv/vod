<?php

namespace App\Http\Controllers;

use App\Classes\AuthorizationService\AuthorizationServiceInterface;
use App\Http\Requests\CheckPermissionExistsRequest;
use App\Http\Requests\checkPermissionRequest;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionController extends Controller
{
    private $authorizeService;

    public function __construct(AuthorizationServiceInterface $authorizeService)
    {
        $this->authorizeService = $authorizeService;
    }

    public function getResponse(checkPermissionRequest $request)
    {
        $this->authorize('askPermission');

        $response = $this->authorizeService->findPermissions($request->get('user_id'), $request->get('permission_id'),
            $request->get('entity_id'));
        return response()->json($response, Response::HTTP_OK);
    }

    public function authorizeWithPermissionName(CheckPermissionExistsRequest $request)
    {
        $access = $this->authorizeService->findPermissionByName(auth()->user(), $request->get('permission'));
        return response()->json(['data' => ['id' => auth()->id(), 'access' => $access]]);
    }
}
