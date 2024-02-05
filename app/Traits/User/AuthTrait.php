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
        return collect([
            'admin',
        ]);
    }

    public function hasRole($role): bool
    {
        $roles = $this->roles;

        return in_array($role, $roles);
    }
}
