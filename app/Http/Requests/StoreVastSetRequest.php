<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreVastSetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->convertStringToArray();
        $this->merge([
            'valid_since' => Carbon::parse($this->get('sinceDate'))->format('Y-m-d'),
            'valid_until' => Carbon::parse($this->get('tillDate'))->format('Y-m-d'),
        ]);
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
            'valid_since' => 'nullable|date',
            'valid_until' => 'nullable|date',
        ];
    }
}
