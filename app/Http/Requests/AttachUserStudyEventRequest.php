<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachUserStudyEventRequest extends FormRequest
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
            'study_method_id' => ['required', 'integer', 'exists:studyevent_methods,id'],
            'major_id' => ['required', 'integer', 'exists:majors,id'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
        ];
    }
}
