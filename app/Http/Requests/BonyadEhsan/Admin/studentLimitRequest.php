<?php

namespace App\Http\Requests\BonyadEhsan\Admin;

use App\Models\User;
use App\Repositories\UserRepo;
use App\Services\BonyadService;
use Illuminate\Foundation\Http\FormRequest;

class studentLimitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $authUser = auth('api')->user();
        $roles = BonyadService::getRoles();
        $rolesKey = collect(array_keys($roles));
        $authUserRoles = $authUser->roles()->pluck('name');
        $userRole = $rolesKey->intersect($authUserRoles)->first();
        if (is_null($userRole) or !UserRepo::userAccess($authUser->id, (int) $this->user_id, $roles[$userRole])) {
            return false;
        }

        if (User::find($this->user_id)->roles()->pluck('name')->contains(config('constants.ROLE_BONYAD_EHSAN_USER'))) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'student_register_limit' => ['required', 'int']
        ];
    }
}
