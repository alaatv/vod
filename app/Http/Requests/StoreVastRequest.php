<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVastRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'videos' => ['required', 'array'],
            'videos.*' => ['mimes:mp4', 'max:10000'],
            'more_info_link' => ['nullable', 'string', 'url'],
            'click_id' => ['nullable', 'required_with:more_info_link', 'string', 'min:2', 'alpha_dash'],
            'click_name' => ['nullable', 'required_with:more_info_link', 'string', 'min:2', 'alpha_dash'],
            'title' => ['nullable', 'string', 'min:2'],
            'is_default' => ['sometimes', 'accepted'],
            'enable' => ['sometimes', 'accepted'],
        ];
    }
}
