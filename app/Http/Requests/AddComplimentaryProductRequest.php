<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddComplimentaryProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'complimentaryproducts' => 'required|exists:products,id',
        ];
    }
}
