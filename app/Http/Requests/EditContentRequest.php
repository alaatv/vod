<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditContentRequest extends FormRequest
{
    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.EDIT_EDUCATIONAL_CONTENT'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            // 'order' => 'required|numeric',
            'name' => 'required',
            // 'grades'=>'required|exists:grades,id',
            // 'majors'=>'required|exists:majors,id',
            // 'contenttypes'=>'required|exists:contenttypes,id',
            'redirectUrl' => 'nullable|string|min:1|max:255',
        ];

        if (isset($this->request->redirectUrl)) {
            $rules['redirectCode'] = 'required|integer|in:'.implode(',',
                array_keys(config('constants.REDIRECT_HTTP_RESPONSE_TYPES')));
        }

        return $rules;
    }
}
