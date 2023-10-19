<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth;

class PasswordRecoveryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (!Auth::check()) {
            $rules = ['mobileNumber' => 'required'];
        } else {
            $rules = [];
        }

        return $rules;
    }
}
