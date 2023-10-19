<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVoucherRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'code' => 'max:20',
            'expirationdatetime' => 'date',
            'enable' => 'max:1',
            'contractor_id' => ['required', 'max:7'],
            'description' => 'nullable|max:200',
            'package_name' => 'nullable|max:50',
            'coupon_id' => ['nullable'],
            'products' => ['nullable'],
        ];
    }
}
