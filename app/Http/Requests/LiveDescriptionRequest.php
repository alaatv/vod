<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LiveDescriptionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string|min:3|max:191',
            'description' => 'required|string|min:3',
            'tags' => 'sometimes|string',
            'entity_id' => 'required|numeric',
            'entity_type' => 'required|string',
            'file' => 'image|max:2000|mimes:jpeg,jpg,png',
            'owner' => ['required', Rule::in(config('constants.ACCEPT_OWNER_FOR_VALIDATION'))],
        ];
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            unset($rules['owner']);
        }

        return $rules;
    }
}
