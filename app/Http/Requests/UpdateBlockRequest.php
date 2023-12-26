<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlockRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tags' => ['array'],
            'title' => ['required', 'string'],
            'customUrl' => ['string'],
            'class' => ['string'],
            'order' => ['numeric'],
            'enable' => ['boolean'],
            'type' => ['required', 'numeric', 'exists:block_types,id'],
        ];
    }
}
