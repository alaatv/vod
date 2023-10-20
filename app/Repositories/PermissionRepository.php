<?php

namespace App\Repositories;

use App\Models\PermissionRole;
use App\Models\PermissionUser;

class PermissionRepository extends AlaaRepo
{
    public static function findLessonsBaseRole($data)
    {
        $parents = self::filterRolesByUser($data)
            ->filter(fn($role) => self::chckParentRules($role, $data['examId']))
            ->pluck('pivot.id')->unique()->toArray();

        return PermissionRole::whereIn('parent_id', $parents)->pluck('accessible_id');
    }

    public static function filterRolesByUser(array $data)
    {
        $permission = self::find($data['permissionId']);
        return $permission
            ? $permission->roles()->whereHas('users', fn($q) => $q->where('id', $data['userId']))->get()
            : collect();
    }

    public static function find($id)
    {
        return self::getModelClass()::find($id);
    }

    public static function getModelClass(): string
    {
        return Permission::class;
    }

    private static function chckParentRules($role, $examId): bool
    {
        return ($role->pivot->accessible_id == $examId || is_null($role->pivot->accessible_id) && is_null($role->pivot->parent_id));
    }

    public static function findLessonsBasePermission($data)
    {
        $parents = PermissionUser::whereNull('parent_id')
            ->where('user_id', $data['userId'])
            ->where('permission_id', $data['permissionId'])
            ->where(function ($q) use ($data) {
                $q->orWhere('accessible_id', $data['examId'])->orWhereNull('accessible_id');
            })->get()->pluck('id')->toArray();

        return PermissionUser::whereIn('parent_id', $parents)->pluck('accessible_id');
    }

    public static function findExamsBaseRole($data)
    {
        return self::filterRolesByUser($data)
            ->pluck('pivot.accessible_id')->unique();
    }

    public static function findExamsBasePermission($data)
    {
        return PermissionUser::where('user_id', $data['userId'])
            ->where('permission_id', $data['permissionId'])
            ->get()->pluck('accessible_id');
    }
}
