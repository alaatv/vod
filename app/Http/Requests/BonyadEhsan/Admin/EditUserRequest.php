<?php

namespace App\Http\Requests\BonyadEhsan\Admin;

use App\Models\Order;
use App\Models\User;
use App\Repositories\UserRepo;
use App\Services\BonyadService;
use App\Traits\CharacterCommon;
use App\Traits\RequestCommon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class EditOrderRequest
 * @package App\Http\Requests
 * @mixin Order
 */
class EditUserRequest extends FormRequest
{
    use CharacterCommon;
    use RequestCommon;

    public const PHOTO_RULE = '|image|mimes:jpeg,jpg,png|max:512';
    protected $id;
    private $userId;

    public function authorize(\App\Http\Requests\Request $request)
    {
        $this->userId = $this->getUserIdFromRequestBody($request)->getValue(false);
        $authUser = auth('api')->user();
        $roles = BonyadService::getRoles();
        $rolesKey = collect(array_keys($roles));
        $authUserRoles = $authUser->roles()->pluck('name');
        $userRole = $rolesKey->intersect($authUserRoles)->first();
        if (is_null($userRole) or !UserRepo::userAccess($authUser->id, (int) $this->userId, $roles[''.$userRole])) {
            return false;
        }

        return true;
    }

    public function rules(Request $request)
    {
        $rules = [
            'firstName' => ['required', 'max:255'],
            'lastName' => ['required', 'max:255'],
            'mobile' => [
                'digits:11', 'required',
                'phone:AUTO,IR',
                Rule::unique('users')
                    ->where(function ($query) {
                        $query->where('nationalCode', $this->request->get('nationalCode'))
                            ->where('id', '<>', $this->userId)
                            ->where('deleted_at', null);
                    }),
            ],
            'nationalCode' => [
                'digits:10', 'required',
                'validate:nationalCode',
                Rule::unique('users')
                    ->where(function ($query) {
                        $query->where('mobile', $this->request->get('mobile'))
                            ->where('id', '<>', $this->userId)
                            ->where('deleted_at', null);
                    }),
            ],
            'shahr_id' => ['required', 'integer', 'min:1', 'exists:shahr,id'],
            'gender_id' => ['required', 'integer', 'min:1', 'exists:genders,id'],
        ];

        $editableUser = User::find($this->userId);
        if ($editableUser->roles()->pluck('name')->contains(config('constants.ROLE_BONYAD_EHSAN_USER'))) {
            $rules = array_merge($rules, [
                'major_id' => ['required', 'integer', 'min:1', 'exists:majors,id'],
                'address' => ['required'],
                'phone' => ['required', 'digits_between:1,15'],
                'fatherMobile' => ['required', 'digits:11', 'phone:AUTO,IR'],
                'motherMobile' => ['required', 'digits:11', 'phone:AUTO,IR'],
            ]);
        }
        return $rules;
    }
}
