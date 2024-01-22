<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CheckoutReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return $this->getRules();
    }

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
