<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditContentSetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'redirectUrl' => 'nullable|string|min:1|max:255',
            'author_id' => 'nullable|exists:users,id',
        ];

        if (isset($this->request->redirectUrl)) {
            $rules['redirectCode'] = 'required|integer|in:' . implode(',',
                    array_keys(config('constants.REDIRECT_HTTP_RESPONSE_TYPES')));
        }

        return $rules;
    }
}
