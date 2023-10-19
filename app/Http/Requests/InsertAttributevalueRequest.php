<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertAttributevalueRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'attribute_id' => 'required|integer|min:1|exists:attributes,id',
            'name' => 'required|string|min:2|max:255',
            'values' => 'nullable|string|min:2|max:191',
            'description' => 'nullable|string|min:2',
            'is_default' => 'nullable|boolean',
            'order' => 'sometimes|integer|min:0',
        ];
    }
}
