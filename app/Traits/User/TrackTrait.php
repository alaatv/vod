<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 17:24
 */

namespace App\Traits\User;



use App\Models\Websitepage;

trait TrackTrait
{
    public function seensitepages()
    {
        return $this->belongsToMany(Websitepage::class, 'userseensitepages', 'user_id', 'websitepage_id')
            ->withPivot('created_at', 'numberOfVisit');
    }

    public function CanSeeCounter(): bool
    {
        return $this->hasRole('admin') ? true : false;
    }
}
