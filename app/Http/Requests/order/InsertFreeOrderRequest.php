<?php

namespace App\Http\Requests\order;

use Illuminate\Foundation\Http\FormRequest;

class InsertFreeOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'products' => ['required', 'array'],
            'products.*' => ['int', 'min:0', 'exists:products,id'],
        ];
    }
}
