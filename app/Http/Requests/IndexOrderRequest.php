<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexOrderRequest extends FormRequest
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
     * @param  Request  $request
     *
     * @return array
     */
    public function rules(Request $request)
    {
        if (!$request->user()->hasRole(config('constants.ROLE_FINANCE_EMPLOYEE'))) {

            return [];
        }
        return [
            'mobile' => 'required_without_all:nationalCode,firstName,lastName,coupons',
            'nationalCode' => 'required_without_all:mobile,firstName,lastName,coupons',
            'firstName' => 'required_without_all:mobile,nationalCode,lastName,coupons',
            'lastName' => 'required_without_all:mobile,nationalCode,firstName,coupons',
            'coupons' => 'required_without_all:mobile,nationalCode,firstName,lastName',
        ];
    }
}
