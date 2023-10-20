<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentSetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'author_id' => 'nullable|exists:users,id',
            'name' => 'required|string',
            'small_name' => 'nullable|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:5000|mimes:jpg,png,jpeg',
            'display' => 'nullable|boolean',
            'enable' => 'nullable|boolean',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'tags' => 'nullable|string',
            'forrest_tree' => ['nullable', 'array'],
            'forrest_tree.*' => 'nullable|string',
            'forrest_tree_tags' => ['nullable', 'array'],
            'forrest_tree_tags.*' => 'nullable|string',
        ];
    }
}
