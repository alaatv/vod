<?php

namespace App\Http\Requests\BonyadEhsan\Exam;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'exam_id' => 'string|required|exists:3a_exams,id',
            'exam_ranking_data' => 'required|numeric',
            'exam_lesson_data' => 'required|array',
            'exam_lesson_data.*.percentage' => 'required|numeric',
            'exam_lesson_data.*.rank' => 'required|numeric',
        ];
    }
}
