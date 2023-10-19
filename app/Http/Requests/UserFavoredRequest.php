<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFavoredRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:content,set,product,timePoint'],
            'limit' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'content_type_ids' => ['nullable', 'array'],
            'content_type_ids.*' => ['integer', 'exists:contenttypes,id'],
            'search' => ['nullable', 'string'],
            'contentset_title' => ['nullable', 'string'],
        ];
    }
}
