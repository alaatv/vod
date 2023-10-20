<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncBlockProductsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'block_products' => ['required', 'array'],
            'block_products.*' => ['required', 'exists:products,id'],

            'block_products_order' => ['array', 'in:[2,3]'],
        ];
    }
}
