<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-10-27
 * Time: 18:44
 */

namespace App\Traits;

use App\Events\FavoriteEvent;
use App\Events\UnfavoriteEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait favorableTraits
{
    public function favoring(User $user, array $data = null): void
    {
        $this->favoriteBy()->syncWithoutDetaching([
            $user->id => $data
        ]);
        event(new FavoriteEvent($user, $this));
    }

    /**
     * Get all of the users that favorite this
     *
     * @return HasMany
     */
    public function favoriteBy()
    {
        return $this->morphToMany(User::class, 'favorable')->withTimestamps();
    }

    public function unfavoring(User $user): void
    {
        $this->favoriteBy()->detach($user);
        event(new UnfavoriteEvent($user, $this));
    }
}
