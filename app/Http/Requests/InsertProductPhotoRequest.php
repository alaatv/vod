<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertProductPhotoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => ['required', 'image', 'max:2000', 'mimes:jpg,png,jpeg'],
            'title' => ['nullable', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'min:2', 'max:1500'],
            'product_id' => ['required', 'exists:products,id'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
