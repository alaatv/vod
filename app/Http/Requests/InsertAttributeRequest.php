<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertAttributeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'displayName' => 'required|string|min:2',
            'description' => 'nullable|string|min:2',
            'attributecontrol_id' => 'required|integer|min:1|exists:attributecontrols,id',
            'attributetype_id' => 'required|integer|min:1|exists:attributetypes,id',
        ];
    }
}
