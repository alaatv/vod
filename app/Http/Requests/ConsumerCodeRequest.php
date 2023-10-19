<?php

namespace App\Http\Requests;

use App\Rules\YaldaReferralCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsumerCodeRequest extends FormRequest
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
            'code' => [
                'required', 'string', Rule::exists('referral_codes', 'code'), new YaldaReferralCode(request()->user())
            ]
        ];
    }


}
