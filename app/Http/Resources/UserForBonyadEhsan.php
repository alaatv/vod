<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\OstanResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserForBonyadEhsan extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        if (! ($this->resource instanceof \App\Models\User)) {
            return [];
        }

        $this->loadMissing('major', 'grade', 'gender');
        $permissions = $this->getPermissionsThroughRoles()->pluck('name')->toArray();

        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'nationalCode' => $this->nationalCode,
            'insertedByFirstName' => $this->insertedBy?->firstName,
            'insertedByLastName' => $this->insertedBy?->lastName,
            'insertedByRoles' => $this->insertedBy?->roles()->pluck('display_name'),
            'phone' => $this->phone,
            'address' => $this->address,
            'fatherMobile' => $this->fatherMobile,
            'motherMobile' => $this->motherMobile,
            'student_register_limit' => $this->consultant?->student_register_limit,
            'student_register_number' => $this->consultant?->student_register_number,
            'mobile' => $this->when(isset($this->mobile), $this->mobile),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'province' => $this->getOstan(),
            'city' => new ShahrLiteResource($this->shahr),
            'major' => new Major($this->major),
            'grade' => new Grade($this->grade),
            'gender' => new Gender($this->gender),
            'mobile_verified_at' => $this->when(isset($this->mobile_verified_at), $this->mobile_verified_at),
            'school' => $this->school,
            'permissions' => $permissions,
            'roles' => $this->getRoles(),
        ];
    }

    private function getOstan()
    {
        $ostan = optional($this->shahr)->ostan;

        return isset($ostan) ? new OstanResource($ostan) : null;
    }
}
