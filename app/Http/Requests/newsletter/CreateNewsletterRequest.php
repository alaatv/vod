<?php

namespace App\Http\Requests\newsletter;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewsletterRequest extends FormRequest
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile' => 'required|digits:11|phone:AUTO,IR',
            'major_id' => 'nullable|exists:majors,id',
            'grade_id' => 'nullable|exists:grades,id',
            'event_id' => 'required|exists:events,id',
            'comment' => 'nullable',
        ];
    }
}
