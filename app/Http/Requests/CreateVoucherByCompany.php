<?php

namespace App\Http\Requests;

use App\Traits\DateTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateVoucherByCompany extends FormRequest
{
    use DateTrait;

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
            'count' => ['required', 'int', 'max:200', 'min:1'],
            'contractor_id' => ['required', 'int', 'exists:contractors,id'],
            'products' => ['required', 'array'],
            'expirationdatetime' => ['required', 'date'],
            'package_name' => ['required', 'string'],
            'coupon_id' => ['required', 'exists:coupons,id']
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge([
            'expirationdatetime' => $this->convertDate($this->expirationdatetime, 'toMiladi'),
            'contractor_id' => $this->contractor,
        ]);
    }
}
