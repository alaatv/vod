<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserWebsiteSettingRequest extends FormRequest
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
            'setting' => 'array',
            'setting.abrisham2_calender_default_lesson' => ['nullable', 'integer', 'exists:products,id'],
        ];
    }
}
