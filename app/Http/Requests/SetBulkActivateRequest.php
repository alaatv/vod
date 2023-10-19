<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetBulkActivateRequest extends FormRequest
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
            'set_ids' => ['required', 'array'],
            'set_ids.*' => ['integer', 'exists:contentsets,id'],
        ];
    }
}
