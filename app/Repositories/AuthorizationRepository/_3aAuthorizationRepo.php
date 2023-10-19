<?php

namespace App\Repositories\AuthorizationRepository;

use App\Repositories\PermissionRepository;

class _3aAuthorizationRepo implements AuthorizationRepoInterface
{
    private $accessables;

    public function findAny($permissionId, $userId, $examId = null): bool
    {
        $args = get_defined_vars();
        return $examId ? $this->hasLessonsPermission($args) : $this->hasExamsPermission($args);
    }

    private function hasLessonsPermission(array $data): bool
    {
        $this->accessables = PermissionRepository::findLessonsBaseRole($data);

        if ($this->accessables->isEmpty()) {
            $this->accessables = PermissionRepository::findLessonsBasePermission($data);
        }

        return $this->accessables->isNotEmpty();
    }

    /////////// Private Area

    private function hasExamsPermission(array $data): bool
    {
        $this->accessables = PermissionRepository::findExamsBaseRole($data);

        if ($this->accessables->isEmpty()) {
            $this->accessables = PermissionRepository::findExamsBasePermission($data);
        }

        return $this->accessables->isNotEmpty();
    }

    public function getAccessibles(): array
    {
        return $this->getResponse();
    }

    private function getResponse(): array
    {
        if ($this->hasFullAccess($this->accessables)) {
            return [];
        }

        return $this->accessables->toArray();
    }

    private function hasFullAccess($collection): bool
    {
        return $collection->count() == 1 && is_null($collection->first());
    }
}
