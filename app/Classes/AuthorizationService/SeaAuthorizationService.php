<?php

namespace App\Classes\AuthorizationService;

use App\Models\User;
use App\Repositories\AuthorizationRepository\AuthorizationRepoInterface;

class SeaAuthorizationService implements AuthorizationServiceInterface
{
    private $authRepo;

    public function __construct(AuthorizationRepoInterface $authorizationRepo)
    {
        $this->authRepo = $authorizationRepo;
    }


    public function findPermissions(int $user_id, int $permission_id, $exam_id = null)
    {
        if (!$this->authRepo->findAny($permission_id, $user_id, $exam_id)) {
            return $this->makeResponse(false, 'denied', []);
        }

        $items = $this->authRepo->getAccessibles();

        if ($items === []) {
            return $this->makeResponse(true, 'full', []);
        }

        return $this->makeResponse(true, 'partial', $items);

    }

    private function makeResponse($access, $access_type, $items)
    {
        return [
            'data' => [
                'access' => $access,
                'access_type' => $access_type,
                'items' => $items
            ]
        ];
    }

    public function findPermissionByName(User $user, string $permission)
    {
        $userPermissions = $user->permissions->pluck('name')->toArray();
        if (is_null($userPermissions)) {
            return false;
        }
        return in_array($permission, $userPermissions);
    }
}
