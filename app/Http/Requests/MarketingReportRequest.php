<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketingReportRequest extends FormRequest
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
            'purchased_products' => ['required', 'array'],
            'purchased_products.*' => ['required', 'integer', 'exists:products,id'],
            'have_bought' => ['required', 'array'],
            'have_bought.*' => ['required', 'integer', 'exists:products,id'],
            'sinceDate' => ['required', 'date'],
            'tillDate' => ['required', 'date'],
        ];
    }
}
