<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            'name' => ['nullable', 'string'],
            'displayName' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'startTime' => ['nullable', 'date'],
            'endTime' => ['nullable', 'date', 'after_or_equal:startTime'],
            'enable' => ['nullable', 'integer', 'in:0,1'],
            'duplicatable' => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
