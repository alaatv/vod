<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContentRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'min:2', 'max:191'],
            'contentset_id' => ['nullable', 'integer', 'exists:contentsets,id'],
            'description' => ['nullable', 'min:2'],
            'thumbnail' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5000'],
            'isFree' => ['boolean'],
            'enable' => ['boolean'],
            'display' => ['boolean'],
            'validSinceDate' => ['nullable', 'date'],
            'forrest_tree' => ['nullable', 'array'],
            'forrest_tree.*' => 'nullable|string',
            'forrest_tree_tags' => ['nullable', 'array'],
            'forrest_tree_tags.*' => 'nullable|string',
            'order' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (is_numeric($value) || 'last' === $value) {
                        return true;
                    }
                    return $fail($attribute.' is invalid.');
                },
            ]
        ];
    }
}
