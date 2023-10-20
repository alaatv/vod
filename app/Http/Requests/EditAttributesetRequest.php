<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditAttributesetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|min:2',
            'description' => 'nullable|string|min:2',
            'order' => 'sometimes|integer|min:0',
        ];
    }
}
