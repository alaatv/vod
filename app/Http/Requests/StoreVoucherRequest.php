<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'code' => 'required|unique:productvouchers|max:20',
            'products' => 'required',
            'expirationdatetime' => 'required|date',
            'enable' => 'required|max:1',
            'contractor_id' => 'required',
            'description' => 'nullable|max:200',
            'package_name' => 'nullable|max:50',
            'coupon_id' => ['required', 'exists:coupons,id']
        ];
    }
}
