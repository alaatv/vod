<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'videos' => 'required|array',
            'videos.*' => 'mimes:mp4',
            'info_link' => 'string|url',
            'info_id' => 'required_with:info_link|string|min:2|alpha_dash',
            'info_name' => 'required_with:info_link|string|min:2|alpha_dash',
        ];
    }
}
