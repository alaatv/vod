<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderproductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'nullable',
            'cost' => 'required|integer|min:0',
            'discountPercentage' => 'required|min:0|max:100',
            'orderproducttype_id' => 'required|integer|exists:orderproducttypes,id',
        ];
    }

    public function prepareForValidation()
    {
        if (is_null($this->input('product_id'))) {
            $this->request->remove('product_id');
        }
    }
}
