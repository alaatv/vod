<?php

namespace App\Http\Requests\OrderProduct;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderproductBatchExtend extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'orderproducts' => ['required', 'array'],
            'orderproducts.*' => [
                'exists:orderproducts,id',
                'distinct',
                Rule::exists('orderProductRenewals', 'orderproduct_id')->whereNull('accepted_at'),
            ],
        ];
    }
}
