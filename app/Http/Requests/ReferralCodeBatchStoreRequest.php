<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReferralCodeBatchStoreRequest extends FormRequest
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
            'number_of_codes' => ['required', 'int'],
            'commission' => ['required', 'int'],
            'mobile' => ['required', 'string'],
            'nationalCode' => ['required', 'string'],
            'discounttype_id' => ['required', 'int', 'exists:discounttypes,id'],
            'firstName' => ['string', 'nullable'],
            'lastName' => ['string', 'nullable'],
        ];
    }
}
