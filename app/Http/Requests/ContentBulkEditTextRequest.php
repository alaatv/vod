<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentBulkEditTextRequest extends FormRequest
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
            'content_ids' => ['required', 'array'],
            'content_ids.*' => ['integer', 'exists:educationalcontents,id'],
            'column' => ['required', 'string'],
            'operation' => ['required', 'string', 'in:concatStart,concatEnd,replace,delete'],
            'text' => ['required', 'string'],
            'replacing_text' => ['nullable', 'required_if:operation,replace', 'string']
        ];
    }
}
