<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyVastSetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->convertStringToArray();
        parent::prepareForValidation();
    }

    protected function convertStringToArray()
    {
        $input = $this->request->all();
        if (isset($input['ids'])) {
            $input['ids'] = explode(',', $input['ids']);
        }
        $this->replace($input);
    }

    public function rules()
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'min:1', 'exists:contentsets,id,deleted_at,NULL'],
        ];
    }
}
