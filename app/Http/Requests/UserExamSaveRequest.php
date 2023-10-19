<?php

namespace App\Http\Requests;

use App\Rules\CheckUserExamWithProduct;
use Illuminate\Foundation\Http\FormRequest;

class UserExamSaveRequest extends FormRequest
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
            'exam_id' => [
                'required',
                'integer',
                'exists:exams,id',
                new CheckUserExamWithProduct(),
            ],
        ];
    }
}
