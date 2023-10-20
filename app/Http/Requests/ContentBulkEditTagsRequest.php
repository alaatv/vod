<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentBulkEditTagsRequest extends FormRequest
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
            'operation' => ['required', 'string', 'in:add,delete,replace'],
            'tags' => ['nullable', 'required_if:operation,add,delete', 'array'],
            'tag' => ['nullable', 'required_if:operation,replace', 'string'],
            'replacing_tag' => ['nullable', 'required_if:operation,replace', 'string'],
        ];
    }
}
