<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveNewBlockRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => ['required', 'integer', 'min:1', 'exists:block_types,id'],
            'title' => ['required', 'string'],
            'tags' => ['nullable', 'sometimes', 'string'],
            'customUrl' => ['nullable', 'sometimes', 'string'],
            'class' => ['nullable', 'sometimes', 'string'],
            'order' => ['integer', 'min:0'],
            'enable' => ['integer', 'min:0'],
        ];
    }
}
