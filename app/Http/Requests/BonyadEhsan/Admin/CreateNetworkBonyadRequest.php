<?php

namespace App\Http\Requests\BonyadEhsan\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateNetworkBonyadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'firstName' => ['required', 'max:255'],
            'lastName' => ['required', 'max:255'],
            'mobile' => ['required', 'digits:11', 'phone:AUTO,IR'],
            'nationalCode' => ['required', 'digits:10', 'validate:nationalCode'],
            'shahr_id' => ['required', 'integer', 'min:1', 'exists:shahr,id'],
            'gender_id' => ['required', 'integer', 'min:1', 'exists:genders,id'],
            'student_register_limit' => ['required', 'integer'],
        ];
    }
}
