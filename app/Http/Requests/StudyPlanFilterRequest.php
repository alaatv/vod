<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudyPlanFilterRequest extends FormRequest
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
            'study_event' => ['integer', 'exists:studyplans,id'],
            'since_date' => 'date',
            'till_date' => 'date',
            'product_id' => ['integer', 'exists:products,id'],
        ];
    }
}
