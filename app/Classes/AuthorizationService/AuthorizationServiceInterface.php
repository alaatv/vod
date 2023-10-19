<?php

namespace App\Classes\AuthorizationService;

use App\Models\User;

interface AuthorizationServiceInterface
{
    public function findPermissions(int $user_id, int $permission_id, $exam_id = null);

    public function findPermissionByName(User $user, string $permission);
}
