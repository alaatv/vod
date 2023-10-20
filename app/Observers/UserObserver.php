<?php

namespace App\Observers;

use App\Events\UserAvatarUploaded;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{

    /**
     * Handle the user "created" event.
     *
     * @param  User  $user
     *
     * @return void
     */
    public function created(User $user)
    {
    }

    /**
     * Handle the user "creating" event.
     *
     * @param  User  $user
     *
     * @return void
     */

    public function creating(User $user)
    {

        $this->photo($user);

    }

    public function photo($user): void
    {

        if (!isset($user->getAttributes()['photo']) || !$user->getAttributes()['photo']) {
            $user->photo = config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE');
        }

    }

    /**
     * Handle the user "updating" event.
     *
     * @param  User  $user
     *
     * @return void
     */

    public function updating(User $user)
    {
        Cache::tags(['user_'.$user->id, 'user_search'])->flush();
        if ($user->checkUserProfileForLocking()) {
            $user->lockHisProfile();
        }

        $this->photo($user);

    }

    /**
     * Handle the user "updated" event.
     *
     * @param  User  $user
     *
     * @return void
     */

    public function updated(User $user)
    {
//        dispatch(new Update3AUserInfo($user));
    }

    /**
     * Handle the user "deleting" event.
     *
     * @param  User  $user
     *
     * @return void
     */
    public function deleting(User $user)
    {


    }

    /**
     * Handle the user "restored" event.
     *
     * @param  User  $user
     *
     * @return void
     */
    public function restored(User $user)
    {
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  User  $user
     *
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }

    /**
     * handle the user "delete photo" event.
     * @param  User  $user
     *
     * return void
     */
    public function deletePhoto(User $user)
    {
        $pathPhoto = $user->photo;

        if ($pathPhoto) {
            event(new UserAvatarUploaded($user, $pathPhoto));
        }

    }


}
