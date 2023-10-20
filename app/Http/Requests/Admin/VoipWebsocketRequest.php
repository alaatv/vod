<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VoipWebsocketRequest extends FormRequest
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
            'caller_phone' => ['required', 'numeric'],
            'user_national_code' => ['numeric'],
            'operator_local_phone' => ['required', 'numeric', 'exists:voip_operators,local_phone_number'],
        ];
    }
}
