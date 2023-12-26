<?php

namespace App\Http\Requests;

use App\Rules\GetAtlesatOneQuailyVastRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVastRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'videos' => ['sometimes', 'array', new GetAtlesatOneQuailyVastRule($this->input('enable'))],
            'more_info_link' => ['nullable', 'string', 'url'],
            'click_id' => ['nullable', 'required_with:more_info_link', 'string', 'min:2', 'alpha_dash'],
            'click_name' => ['nullable', 'required_with:more_info_link', 'string', 'min:2', 'alpha_dash'],
            'title' => ['nullable', 'string', 'min:2'],
            'is_default' => ['sometimes', 'accepted'],
            'enable' => ['sometimes', 'accepted'],
        ];
    }
}
