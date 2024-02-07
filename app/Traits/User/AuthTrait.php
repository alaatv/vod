<?php

namespace App\Traits\User;

use Illuminate\Support\Collection;

trait AuthTrait
{
    public function getRolesAttribute(): Collection
    {
        //TODO:// fix can roles
        return collect([
            'admin',
        ]);
    }

    public static function getUserWithPermissions(array $permissions): ?Collection
    {
        //TODO::// connect to auth service
        return null;
    }

    public static function getUserWithRoles(array $roles): ?Collection
    {
        //TODO::// connect to auth service
        return null;
    }

    public static function getUserWithRolesAndPermissions(array $roles, array $permissions): ?Collection
    {
        //TODO::// connect to auth service
        return null;
    }

    public function isAbleTo(string $can): bool
    {
        //TODO:// fix can permission
        return true;

        $permissions = $this->permissions();

        return in_array($can, $permissions);
    }

    public function permissions(): Collection
    {
        //TODO:// fix can roles
        return $this->permissions;
    }

    public function getPermissionsAttribute(): Collection
    {
        //TODO:// fix can roles
        return collect([
            'admin',
        ]);
    }

    public function hasRole($role): bool
    {
        $roles = $this->roles->toArray();

        return in_array($role, $roles);
    }

    public function hasPermission($permission): bool
    {
        $permissions = $this->permissions->toArray();

        return in_array($permission, $permissions);
    }
}
