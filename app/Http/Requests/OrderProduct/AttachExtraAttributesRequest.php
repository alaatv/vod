<?php

namespace App\Http\Requests\OrderProduct;

use Illuminate\Foundation\Http\FormRequest;

class AttachExtraAttributesRequest extends FormRequest
{

    /**
     * This middleware is not being used at this time
     * It's syntax has been archived fot futher uses
     */
    public function authorize()
    {
        $user = auth()->user();
        if ($user) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $rules = [
            'extraAttribute' => 'required|array',
            'extraAttribute.*' => 'required|array',
            'extraAttribute.*.id' => 'required|numeric',
            'extraAttribute.*.cost' => 'required|numeric',
        ];

        return $rules;
    }
}
