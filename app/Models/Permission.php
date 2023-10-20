<?php

use App\Traits\DateTrait;
use App\Traits\Helper;

class Permission extends LaratrustPermission
{
    use Helper;
    use DateTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'service_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Config::get('laratrust.models.role'),
            Config::get('laratrust.tables.permission_role'),
            Config::get('laratrust.foreign_keys.permission'),
            Config::get('laratrust.foreign_keys.role')
        )->withPivot('accessible_id', 'parent_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(
            Config::get('laratrust.user_models.users'),
            Config::get('laratrust.tables.permission_user'),
            Config::get('laratrust.foreign_keys.permission'),
            Config::get('laratrust.foreign_keys.user')
        )->withPivot('accessible_id', 'parent_id', 'id');
    }
}
