<?php

namespace App\Repositories\AuthorizationRepository;

interface AuthorizationRepoInterface
{
    public function findAny($permissionId, $userId, $examId = null): bool;

    public function getAccessibles(): array;
}
