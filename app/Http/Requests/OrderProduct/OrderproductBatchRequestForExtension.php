<?php

namespace App\Http\Requests\OrderProduct;

use App\Models\Orderproduct;
use Illuminate\Foundation\Http\FormRequest;

class OrderproductBatchRequestForExtension extends FormRequest
{
    public $order_products;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'orderproducts' => ['required', 'array'],
            'orderproducts.*' => ['exists:orderproducts,id', 'distinct'],
            'order_products_expired' => 'required|in:'.count($this->orderproducts),
            'konkurYear' => ['required', 'exists:events,id'],
            'studentOrGraduate' => ['required', 'exists:eventParticipantGroups,id'],
            'karteMeli_photo' => ['image', 'max:5000', 'mimes:jpg,png,jpeg'],
        ];
    }

    public function prepareForValidation()
    {
        $this->order_products = Orderproduct::whereIn('id', $this->orderproducts)->whereDoesntHave('renewals',
            function ($q) {
                $q->notAccepted();
            })->expired()->get();

        $this->merge([
            'order_products_expired' => $this->order_products->count(),
        ]);
    }
}
