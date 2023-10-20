<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertZarinpalTransaction extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id' => 'required',
            'cost' => 'required|integer',
            'refId' => 'required|integer',
            'authority' => 'required|string',
        ];
    }
}
