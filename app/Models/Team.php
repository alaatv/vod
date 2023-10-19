<?php

namespace App\Models;


use Laratrust\LaratrustTeam;

class Team extends LaratrustTeam
{
    public const SUPPORT_TEAM_ID = 1;

    public function roles()
    {
        return $this->belongsToMany(Role::Class, 'role_user', 'team_id');
    }
}
