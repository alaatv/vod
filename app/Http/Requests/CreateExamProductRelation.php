<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExamProductRelation extends FormRequest
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
            'id' => [
                'string',
                'min:3',
                'max:25',
//                Rule::unique('3a_exams')->ignore($this->title, 'title')
            ],
            'title' => ['string', 'min:3'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->exam_id,
            'title' => $this->exam_title,
        ]);
    }

    public function attributes()
    {
        return [
            'id' => 'شناسه آزمون',
            'title' => 'عنوان آزمون'
        ];
    }

    public function messages()
    {
        return [
            'string' => ':attribute بایستی حروف باشد',
            'title' => ':attribute بایستی حروف باشد',
        ];
    }
}
