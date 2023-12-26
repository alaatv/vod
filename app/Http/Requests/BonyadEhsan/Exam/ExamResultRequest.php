<?php

namespace App\Http\Requests\BonyadEhsan\Exam;

use App\Rules\BonyadUserAccess;
use App\Rules\ShowLevelAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamResultRequest extends FormRequest
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
            'user_id' => ['bail', 'nullable', 'exists:users,id', new BonyadUserAccess($this->user())],
            'major' => ['bail', 'nullable', 'exists:majors,id'],
            'first_name' => ['bail', 'nullable'],
            'last_name' => ['bail', 'nullable'],
            'mobile' => ['bail', 'nullable'],
            'national_code' => ['bail', 'nullable'],
            'action' => [
                'bail', 'nullable', Rule::in(['show-networks', 'show-subnetworks', 'show-moshavers', 'show-students']),
                new ShowLevelAccess($this->user()),
            ],
        ];
    }
}
