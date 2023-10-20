<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Rules\UniqueNationalCodeWithPhoneInTrashBinRule;
use App\Rules\UniqueNationalCodeWithPhoneRule;
use App\Traits\RequestCommon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserApiRequest extends FormRequest
{
    use RequestCommon;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        $this->userId = $this->getUserIdFromRequestBody($request)->getValue(false);

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
            'firstName' => 'nullable|string|min:2|max:255',
            'lastName' => 'nullable|string|min:2|max:255',
            'mobile' => [
                'required',
                'digits:11',
                Rule::phone()
                    ->mobile()
                    ->country('AUTO,IR'),
            ],
            'nationalCode' => [
                'required',
                'digits:10',
                'validate:nationalCode',
                new UniqueNationalCodeWithPhoneRule($this->userId,
                    $this->request->get('nationalCode'),
                    $this->request->get('phone')
                ),
                new UniqueNationalCodeWithPhoneInTrashBinRule($this->userId,
                    $this->request->get('nationalCode'),
                    $this->request->get('phone')
                ),
            ],
            'password' => 'sometimes|nullable|min:10|confirmed',
            'userstatus_id' => 'required|exists:userstatuses,id',
            'postalCode' => 'sometimes|nullable|numeric',
            'email' => 'sometimes|nullable|email',
            'major_id' => 'sometimes|nullable|exists:majors,id',
            'grade_id' => 'sometimes|nullable|exists:grades,id',
            'gender_id' => 'sometimes|nullable|exists:genders,id',
            'school' => 'sometimes|nullable|string|max:191',
            'address' => 'sometimes|nullable|string|max:1200',
            'bio' => 'sometimes|nullable|string|max:5000',
            'birthdate' => 'sometimes|nullable|date_format:Y-m-d',
        ];
    }
}
