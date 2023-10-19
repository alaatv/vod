<?php

namespace App\Http\Requests;

use App\Rules\ProductSetDiscountRule;
use Illuminate\Foundation\Http\FormRequest;

class SetProductDiscountRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'discount' => ['required', 'integer', 'max:100', 'min:0'],
            'products' => ['required', 'array', new ProductSetDiscountRule()],
            'products.*' => ['required', 'exists:products,id'],
        ];
    }


}
