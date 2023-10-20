<?php

namespace App\Policies;

use App\Models\Draft;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DraftPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Draft $draft)
    {
        return $user->hasRole('admin') || ($user->id == $draft->author->id);
    }

    public function create(User $user)
    {
        // todo: add any role that can create draft
        return $user->hasRole(['admin']);
    }

    public function update(User $user, Draft $draft)
    {
        return $user->hasRole('admin') || ($user->id == $draft->author->id);
    }

    public function delete(User $user, Draft $draft)
    {
        return $user->hasRole('admin') || ($user->id == $draft->author->id);
    }

    public function restore(User $user, Draft $draft)
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Draft $draft)
    {
        return $user->hasRole('admin') || ($user->id == $draft->author->id);
    }
}
