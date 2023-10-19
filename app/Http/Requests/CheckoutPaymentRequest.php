<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth;

class CheckoutPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return $this->getRules();
    }

    /**
     * @return array
     */
    private function getRules(): array
    {
        $user = Auth::user();
        if (isset($user)) {
            $rules = [
                'order_id' => 'required',
            ];
        } else {
            $rules = [];
        }

        return $rules;
    }
}
