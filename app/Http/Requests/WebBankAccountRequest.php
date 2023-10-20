<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebBankAccountRequest extends FormRequest
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
            'accountNumber' => 'nullable|numeric|unique:bankaccounts,accountNumber',
            'cardNumber' => 'nullable|numeric|unique:bankaccounts,cardNumber',
            'preShabaNumber' => 'required',
            'shabaNumber' => 'required|digits:24|unique:bankaccounts,shabaNumber',
        ];
    }
}
