<?php

namespace App\Http\Requests\BonyadEhsan\Admin;

use App\Rules\BonyadUserUnique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGroupUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth('api')->user();

        return match ($this->type) {
            'network' => $user->hasPermission(config('constants.BONYAD_EHSAN_INSERT_NETWORK')),
            'subnetwork' => $user->hasPermission(config('constants.BONYAD_EHSAN_INSERT_SUB_NETWORK')),
            'moshaver' => $user->hasPermission(config('constants.BONYAD_EHSAN_INSERT_MOSHAVER')),
            'student' => $user->hasPermission(config('constants.BONYAD_EHSAN_INSERT_USER')),
            default => false,
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type' => Rule::in(config('constants.BONYAD_USER_TYPES')),
            'users' => ['required', 'array'],
            'users.*' => ['required'],
            'users.*.firstName' => ['required', 'max:255'],
            'users.*.lastName' => ['required', 'max:255'],
            'users.*.mobile' => ['required', 'digits:11', 'phone:AUTO,IR', new BonyadUserUnique()],
            'users.*.nationalCode' => ['required', 'digits:10', 'validate:nationalCode'/*'unique:users'*/],
            'users.*.shahr_id' => ['required', 'integer', 'min:1', 'exists:shahr,id'],
            'users.*.gender_id' => ['required', 'integer', 'min:1', 'exists:genders,id'],
        ];

        if ($this->type == 'network' or $this->type == 'subnetwork' or $this->type == 'moshaver') {
            $rules = array_merge($rules, [
                'users.*.student_register_limit' => ['required', 'integer'],
            ]);
        } else {
            if ($this->type == 'student') {
                $rules = array_merge($rules, [
                    'users.*.major_id' => ['required', 'integer', 'min:1', 'exists:majors,id'],
                    'users.*.address' => ['required'],
                    'users.*.phone' => ['required', 'digits_between:1,15'],
                    'users.*.father_mobile' => ['required', 'digits:11', 'phone:AUTO,IR'],
                    'users.*.mother_mobile' => ['required', 'digits:11', 'phone:AUTO,IR'],
                ]);
            }
        }

        return $rules;
    }
}
