<?php

namespace App\Traits\User;

trait AuthTrait
{
    public function getRolesAttribute(): array
    {
        //TODO:// fix can roles
        return [
            'admin'
        ];
    }

    public function isAbleTo(string $can): bool
    {
        //TODO:// fix can permission
        return true;

        $permissions = $this->permissions();
        return in_array($can, $permissions);
    }

    public function permissions(): array
    {
        //TODO:// fix can roles
        return [
            'admin'
        ];
    }

    public function hasRole($role): bool
    {
        $roles = $this->roles;
        return in_array($role, $roles);
    }
}