<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\OstanResource;
use Illuminate\Http\Request;

/**
 * Class User
 *
 * @mixin \App\User
 * */
class UserFor3A extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\User)) {
            return [];
        }

        $this->loadMissing('major', 'grade', 'gender');

        $roles = $this->roles()->get();
        $roles = isset($roles) ? $roles->pluck('name')->toArray() : null;
        $permissions = $this->getPermissionsThroughRoles();
        $permissions = isset($permissions) ? $permissions->pluck('name')->toArray() : null;

        $hasAdminPermission = (isset($roles)) && (in_array(config('constants.ROLE_ADMIN'),
                    $roles) || in_array(config('constants.ROLE_3A_MANAGER'), $roles));
        $hasEducationalPermission = (isset($roles)) && in_array(config('constants.ROLE_3A_EDUCATIONAL_EMPLOYEE'),
                $roles);

        return [
            'id' => $this->id,
            'first_name' => $this->when(isset($this->firstName), $this->firstName),
            'last_name' => $this->when(isset($this->lastName), $this->lastName),
            'mobile' => $this->when(isset($this->mobile), $this->mobile),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'province' => $this->getOstan(),
            'city' => new ShahrLiteResource($this->shahr),
            'major' => $this->when(isset($this->major), function () {
                return new Major($this->major);
            }),
            'grade' => $this->when(isset($this->grade), function () {
                return new Grade($this->grade);
            }),
            'gender' => $this->when(isset($this->gender), function () {
                return new Gender($this->gender);
            }),
            'has_admin_permission' => $hasAdminPermission,
            'mobile_verified_at' => $this->when(isset($this->mobile_verified_at), $this->mobile_verified_at),
            'school' => $this->when(isset($this->school), $this->school),
            'roles' => $roles,
            'permissions' => $permissions,
            'has_educational_permission' => $hasEducationalPermission,
            'updated_at' => $this->updated_at,
        ];
    }

    private function getOstan()
    {
        $ostan = optional($this->shahr)->ostan;

        return isset($ostan) ? new OstanResource($ostan) : null;
    }
}
