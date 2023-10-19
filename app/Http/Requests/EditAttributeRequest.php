<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditAttributeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|min:2',
            'displayName' => 'sometimes|string|min:2',
            'description' => 'nullable|string|min:2',
            'attributecontrol_id' => 'sometimes|integer|min:1|exists:attributecontrols,id',
            'attributetype_id' => 'sometimes|integer|min:1|exists:attributetypes,id',
        ];
    }
}
