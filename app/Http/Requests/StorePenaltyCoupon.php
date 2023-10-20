<?php

namespace App\Http\Requests;

use App\Rules\PenaltyCouponHashRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePenaltyCoupon extends FormRequest
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
            'token' => ['required', new PenaltyCouponHashRule()],
            'coupon' => ['required', 'array'],
            'coupon.*.code' => ['required', 'string'],
            'coupon.*.name' => ['required', 'string'],
            'coupon.*.description' => ['required', 'string'],
            'coupon.*.discount' => ['required', 'numeric'],
        ];
    }
}
