<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use function Aws\boolean_value;

class ProductConfigRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'products' => ['required', Rule::when(! in_array(0, $this->get('products', [])), 'exists:products,id')],
            'enable' => ['boolean'],
            'display' => ['boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('enability')) {
            $this->merge([
                'enable' => boolean_value($this->get('enability')),
            ]);
        }
        if ($this->has('display')) {
            $this->merge([
                'display' => boolean_value($this->get('display')),
            ]);
        }
    }
}
